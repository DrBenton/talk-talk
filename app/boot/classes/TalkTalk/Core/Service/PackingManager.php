<?php

namespace TalkTalk\Core\Service;

class PackingManager extends BaseService
{

    const PACK_FILES_EXTENSION = '.pack.php';
    const PHP_NS_DECLARATION_PATTERN = '~namespace\s+([A-Z][a-zA-Z0-9_\\\\]+)\s*;~';
    const PHP_NS_WITH_BRACKET_DECLARATION_PATTERN = '~namespace\s*([A-Z][a-zA-Z0-9_\\\\]+)?\s*{~';
    const PHP_NS_USE_PATTERN = '~use\s+([A-Z][a-zA-Z0-9_\\\\]+\s*(?:\s+as\s+[A-Z][a-zA-Z0-9_]+\s*)?)[,;]~';
    const PHP_CLASS_PATTERN = '~^\s*(?:abstract\s+)?class\s+([A-Z][a-zA-Z0-9_]+)~m';
    const PHP_INTERFACE_PATTERN = '~^\s*interface\s+([A-Z][a-zA-Z0-9_]+)~m';

    /**
     * @var string
     */
    protected $packsDir;

    public function setPacksDir($packsDir)
    {
        $this->packsDir = $packsDir;
    }

    /**
     * @param mixed  $data
     * @param string $targetNamespace
     * @param string $targetId
     */
    public function packData($data, $targetNamespace, $targetId)
    {
        $dataAsPhp = var_export($data, true);
        $dataPackContent = "return $dataAsPhp;";
        $this->packPhpCode($dataPackContent, $targetNamespace, $targetId);
    }

    /**
     * @param string $filesPaths
     * @param string $targetNamespace
     * @param string $targetId
     */
    public function packPhpFiles($filesPaths, $targetNamespace, $targetId)
    {
        $this->packPhpCode($this->getPhpFilesCode($filesPaths), $targetNamespace, $targetId);
    }

    /**
     * @param $filesPaths
     * @return string
     */
    public function getPhpFilesCode($filesPaths)
    {
        $code = '';
        $rootPath = $this->app->vars['app.root_path'];
        foreach ($filesPaths as $phpFilePath) {
            if (0 !== strpos($phpFilePath, $rootPath)) {
                $phpFilePath = $rootPath . '/' . $phpFilePath;
            }

            $code .= $this->getPhpFileContentForPacking($phpFilePath);
        }

        return $code;
    }

    /**
     * @param array $filesToIncludeInPaths
     * @param $targetNamespace
     * @param $targetId
     */
    public function packAppInclusions(array $filesToIncludeInPaths, $targetNamespace, $targetId)
    {
        $this->packPhpCode($this->getAppInclusionsCode($filesToIncludeInPaths), $targetNamespace, $targetId);
    }

    /**
     * @param  array $filesToIncludeInPaths
     * @param  array $additionalInclusionFunctionArgs
     * @throws \DomainException
     * @return string
     */
    public function getAppInclusionsCode($filesToIncludeInPaths, $additionalInclusionFunctionArgs = array())
    {
        $filesToIncludeInPaths = (array) $filesToIncludeInPaths;
        $code = '';
        foreach ($filesToIncludeInPaths as $phpFileToIncludePath) {

            if (!file_exists($phpFileToIncludePath)) {
                throw new \DomainException(sprintf('PHP file to include in app "%s" not found!', $phpFileToIncludePath));
            }
            $fileContent = file_get_contents($phpFileToIncludePath);

            // First php opening tag removal
            $fileContent = $this->stripOpeningPhpTag($fileContent, $phpFileToIncludePath);

            // Imported Namespaces management (we can't use "use" in Closures)
            $importedNamespacesStrippingResult = $this->stripImportedNamespaces($fileContent, $phpFileToIncludePath);
            $importedNamespacesStr = implode(PHP_EOL,
                array_map(
                    function ($strippedNamepace) {
                        return PHP_EOL . 'use ' . $strippedNamepace . ';' . PHP_EOL ;
                    },
                    $importedNamespacesStrippingResult['strippedNamespaces']
                )
            );
            $fileContent = $importedNamespacesStrippingResult['content'];

            // Do we have additional args to transmit to this Closure?
            $args = '';
            if (!empty($additionalInclusionFunctionArgs)) {
                $args = ', ' . implode(', ', $additionalInclusionFunctionArgs);
            }

            // Go!
            $filePath = $this->app->appPath($phpFileToIncludePath);
            $fileContent = <<<END
namespace {
    /* begin file to include in app closure: "$filePath" */
    $importedNamespacesStr
    \$app->vars['packs.included_files.closures']['$filePath'] = function (\$app$args) {
        $fileContent
    };
    /* end file to include in app closure: "$filePath" */
}

END;
            $code .= $fileContent;
        }

        return $code;
    }

    /**
     * @param string $rawPhpCode
     * @param string $targetNamespace
     * @param string $targetId
     */
    public function packPhpCode($rawPhpCode, $targetNamespace, $targetId)
    {
        $generator = __METHOD__;
        $now = date('Y-m-d H:i:s');

        $securityFileHead = <<<END
if (!defined('APP_ENVIRONMENT')) {
    die('Unauthorized access');
}
END;
        if (preg_match(self::PHP_NS_WITH_BRACKET_DECLARATION_PATTERN, $rawPhpCode)) {
            $securityFileHead = 'namespace {' . PHP_EOL .
                $securityFileHead .
                PHP_EOL . '}' . PHP_EOL ;
        }

        $rawPhpCode = "<?php
/* Generated by $generator at $now */

$securityFileHead

$rawPhpCode
";
        $targetFilePath = $this->getPackDataFilePath($targetNamespace, $targetId);
        $targetDir = dirname($targetFilePath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        file_put_contents($targetFilePath, $rawPhpCode);
    }

    /**
     * @param  string $targetNamespace
     * @param  string $targetId
     * @return mixed
     */
    public function unpackData($targetNamespace, $targetId)
    {
        $packedDataFilePath = $this->getPackDataFilePath($targetNamespace, $targetId);

        return $this->app->includeInApp($packedDataFilePath);
    }

    /**
     * @param  string $targetNamespace
     * @param  string $targetId
     * @return bool
     */
    public function hasPackedData($targetNamespace, $targetId)
    {
        $packedDataFilePath = $this->getPackDataFilePath($targetNamespace, $targetId);

        return file_exists($packedDataFilePath);
    }

    public function getPackDataFilePath($targetNamespace, $targetId)
    {
        if (
            false !== strpos($targetNamespace, '.') ||
            false !== strpos($targetId, '.') ||
            false !== strpos($targetId, '/')
        ) {
            throw new \RuntimeException(sprintf('Invalid packing namespace "%s" / id "%s"!', $targetNamespace, $targetId));
        }

        return $this->packsDir . '/' . $targetNamespace . '/' . $targetId . self::PACK_FILES_EXTENSION;
    }

    public function getPhpFileContentForPacking($phpFilePath)
    {
        if (!file_exists($phpFilePath)) {
            throw new \DomainException(sprintf('PHP file to format for packing "%s" not found!', $phpFilePath));
        }
        $phpFileContent = file_get_contents($phpFilePath);

        // First php opening tag removal
        $phpFileContent = $this->stripOpeningPhpTag($phpFileContent, $phpFilePath);

        // Namespace management
        list($namespaceName, $phpFileContent) = $this->handleClassNamespace($phpFileContent);

        // Let's wrap this file class/interface definition in a "if (class_exists()) {...}"
        $phpFileContent = $this->wrapClassDefinitionInClassExistsCheck($phpFileContent, $namespaceName);

        // __DIR__ quick'n'dirty management
        $phpFileContent = str_replace('__DI'.'R__', '\'' . dirname($phpFilePath) . '\'', $phpFileContent);

        // A small "packing" comment is appended to the file
        $phpFileContent .= PHP_EOL . "/* @end \"$phpFilePath\" */" . PHP_EOL . PHP_EOL;

        return $phpFileContent;
    }

    public function stripOpeningPhpTag($phpFileContent, $phpFilePath = null)
    {
        // First php opening tag removal
        if ('<?php' === substr($phpFileContent, 0, 5)) {
            $phpFileContent = substr($phpFileContent, 5);
            if (null !== $phpFilePath) {
                $phpFileContent = PHP_EOL . "/* @begin \"$phpFilePath\" */" . PHP_EOL . $phpFileContent;
            }
        }

        return $phpFileContent;
    }

    public function stripImportedNamespaces($phpFileContent, $phpFilePath)
    {
        $strippedNamespaces = array();
        $phpFileContent = preg_replace_callback(
            self::PHP_NS_USE_PATTERN,
            function (array $matches) use (&$strippedNamespaces) {
                $importedNamespaceName = $matches[1];
                $strippedNamespaces[] = $importedNamespaceName;

                return '/* moved "' . $importedNamespaceName . '" */' . PHP_EOL ;
            },
            $phpFileContent
        );

        return array(
            'content' => $phpFileContent,
            'strippedNamespaces' => $strippedNamespaces
        );
    }

    protected function handleClassNamespace($phpFileContent)
    {
        $namespaceName = null;
        $phpFileContent = preg_replace_callback(
            self::PHP_NS_DECLARATION_PATTERN,
            function (array $matches) use (&$namespaceName) {
                $namespaceName = $matches[1];

                return 'namespace ' . $namespaceName. ' {' . PHP_EOL ;
            },
            $phpFileContent
        );
        if (null !== $namespaceName) {
            // Close the namespace with a bracket
            $phpFileContent .= PHP_EOL . '} //end namespace "' . $namespaceName . '"' . PHP_EOL ;
        } elseif (!preg_match('~^\s*namespace\s+~m', $phpFileContent)) {
            // No namespace? Let's enclose this PHP file content in a root one!
            $phpFileContent = str_replace(PHP_EOL, PHP_EOL . '    ', $phpFileContent);
            $phpFileContent = 'namespace {' . PHP_EOL . $phpFileContent . PHP_EOL . '} //end namespace' . PHP_EOL ;
        }

        return array($namespaceName, $phpFileContent);
    }

    /**
     * This method content is not very clean, but... it seems to work :-)
     *
     * @param  string $phpFileContent
     * @param  string $classNamespace
     * @return string
     */
    protected function wrapClassDefinitionInClassExistsCheck($phpFileContent, $classNamespace)
    {
        $classNsPrefix = (null === $classNamespace) ? '' : $classNamespace . '\\' ;

        // "class_exists()" checks around class/interface definition, *inside* their namespace
        $definitionsPatterns = array(self::PHP_CLASS_PATTERN, self::PHP_INTERFACE_PATTERN);
        if (strpos($classNamespace, 'Container')) {
            define('HOP', 1);
        }

        foreach ($definitionsPatterns as $definitionPattern) {

            if (preg_match($definitionPattern, $phpFileContent)) {

                $methodToUse = ($definitionPattern === self::PHP_CLASS_PATTERN)
                    ? 'class_exists'
                    : 'interface_exists';

                $nbClassesDefinitionsHandled = 0;
                $phpFileContent = preg_replace_callback(
                    $definitionPattern,
                    function ($matches) use ($classNsPrefix, $methodToUse, &$nbClassesDefinitionsHandled) {
                        $className = $matches[1];
                        $nbClassesDefinitionsHandled++;

                        return PHP_EOL . "if (!$methodToUse('$classNsPrefix$className', false)) {" . PHP_EOL . $matches[0];
                    },
                    $phpFileContent
                );

                // We have to close this "if"..
                /*.
                $phpFileContent = preg_replace_callback(
                    '~(^|{)}$~m',
                    function ($matches) use ($classNsPrefix, $methodToUse) {
                        $className = $matches[1];

                        return PHP_EOL .'}' . PHP_EOL . '}//end if (!class_exists(...))' . PHP_EOL ;
                    },
                    $phpFileContent
                );
                */
                // (let's hope this file contains only one class!)
                $lastClosingBracketPos = strrpos($phpFileContent, '}');
                $phpFileContent = substr_replace(
                    $phpFileContent,
                    PHP_EOL . "}//end if (!class_exists('...'))" . PHP_EOL . '}',
                    $lastClosingBracketPos
                );

                if ($nbClassesDefinitionsHandled > 1) {

                    // More than 1 class/interface definition in a single PHP file!
                    // Ach! Das ist verbotten!

                    // Is this a Illuminate file with an embedded "mini-class"?
                    $embeddedMiniClassPattern =
                        '~\s*(?:abstract\s+)?class\s+[A-Z][a-zA-Z0-9_]+'.
                        '(?:\s+extends\s+[\\a-zA-Z0-9]+)?'.
                        '(?:\s+implements\s+[\\a-zA-Z0-9 ,]+)?'.
                        '\s+\{\s*\}~'
                    ;
                    if (preg_match($embeddedMiniClassPattern, $phpFileContent, $matches)) {
                        // Taylor, why did you put 2 classes definitions in your Illuminate\Container?
                        // This cost me hours of additional debugging! :-)
                        $phpFileContent = str_replace($matches[0], $matches[0] . PHP_EOL . '}//end if (!micro_class_exists(...))' . PHP_EOL, $phpFileContent);
                    } else {
                        throw new \DomainException(
                            sprintf('%d class/interface definitions found in a single PHP file!', $nbClassesDefinitionsHandled)
                        );
                    }

                }//end multiple classes resolution

            }

        }

        return $phpFileContent;
    }
}
