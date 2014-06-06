<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;

class ClassesPacker extends BasePacker
{

    protected $myConfigKey = '@classes';

    /**
     * @var \ReflectionMethod
     */
    protected $getOrderedClasses;

    public function __construct()
    {
        // We want to use Symfony ClassCollectionLoader's "getOrderedClasses()" private method.
        // Thanks to this method, our classes will follow their dependencies relations order.

        // --> Let's make it accessible via Reflection, and store a reference to this method!
        // (yeah, Symfony loves private methods instead of protected ones :-/ )
        $reflectionClass = new \ReflectionClass('Symfony\Component\ClassLoader\ClassCollectionLoader');
        $this->getOrderedClasses = $reflectionClass->getMethod('getOrderedClasses');
        $this->getOrderedClasses->setAccessible(true);
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(Plugin $plugin)
    {
        if (empty($plugin->config[$this->myConfigKey])) {
            return null;
        }

        $myConfigPart = $plugin->config[$this->myConfigKey];

        $code = '';
        foreach ($myConfigPart as $classesData) {
            $code .= $this->getClassesPhpCode($plugin, $classesData);
        }

        return $code;
    }

    protected function getClassesPhpCode(Plugin $plugin, array $classesData)
    {
        $classesBasePath = $this->app
            ->get('utils.string')
            ->handlePluginRelatedString($plugin, $classesData['path']);

        $classesToIncludesPaths = $this->app
            ->get('utils.io')
            ->rglob('*.php', $classesBasePath);

        $nbClassesToInclude = count($classesToIncludesPaths);

        // Symfony ClassCollectionLoader needs to be able to work with our classes.
        // --> let's make them accessible via Composer!
        $this->app->get('autoloader')->addPsr4(
            $classesData['prefix'],
            $classesBasePath
        );

        // We build a "class name" => "class file path" map
        $classesNamesFilesPathsMap = array();
        foreach ($classesToIncludesPaths as $classFilePath) {
            //TODO: make it cleaner
            $className = str_replace(
                array(DIRECTORY_SEPARATOR, '.php'),
                array('\\', ''),
                preg_replace('~^.*/classes/~', '', $classFilePath)
            );
            $classesNamesFilesPathsMap[$className] = $classFilePath;
        }

        $classesNames = array_keys($classesNamesFilesPathsMap);

        // We want to be sure that all these classes are loaded before Symfony's ClassCollectionLoader work
        foreach ($classesNames as $className) {

            if (class_exists($className, false)) {
                continue;
            }

            $this->app
                ->get('autoloader')
                ->loadClass($className);

        }

        // Please, Symfony, order our classes by their hierarchy!
        $orderedClassesNames = $this->getOrderedClasses->invoke(null, $classesNames);

        // All right, now we just have to include these classes in the PHP Plugin pack
        $classesToIncludeCode = '';
        foreach ($orderedClassesNames as $classReflection) {

            $className = $classReflection->getName();

            // Is this one of the classes we have to load?
            if (!in_array($className, $classesNames)) {
                continue;
            }

            $classFilePath = $classesNamesFilesPathsMap[$className];

            // Class content formatting
            $classContent = $this->app
                ->get('packing-manager')
                ->getPhpFileContentForPacking($classFilePath);

            // The formatted PHP Class content is appended to our packed Plugin PHP code
            $classesToIncludeCode .= $classContent;

        }

        /*
        // We also add the class resolution scheme to Composer as a fallback, just in case...
        $classesToIncludeCode .= <<<PLUGIN_PHP_CODE
namespace {
    \$app->get('autoloader')->addPsr4(
        '$classesData[prefix]\',
        '$classesBasePath'
    );
}
PLUGIN_PHP_CODE;
        */

        $classesToIncludeCode .= PHP_EOL . "/* end $nbClassesToInclude PHP Classes inclusions of plugin $plugin->id, path '$classesBasePath' */" . PHP_EOL ;

        return $classesToIncludeCode;
    }

}
