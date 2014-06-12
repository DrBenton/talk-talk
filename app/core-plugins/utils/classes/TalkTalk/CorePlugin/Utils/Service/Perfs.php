<?php

namespace TalkTalk\CorePlugin\Utils\Service;

use TalkTalk\Core\Service\BaseService;
use TalkTalk\Core\Service\PackingManager;

class Perfs extends BaseService
{

    /**
     * @return int the number of milliseconds elasped since the "app.php" inclusion
     */
    public function getElapsedTime()
    {
        return round((microtime(true) - $this->app->vars['perfs.start_time']) * 1000);
    }

    public function getNbIncludedFiles()
    {
        $includedFiles = get_included_files();

        $templatesFilesExt = '.' . $this->app->vars['view.templates_ext'];
        $packsFilesExt = PackingManager::PACK_FILES_EXTENSION;
        $nbIncludedTemplates = $nbIncludedPacks = 0;
        foreach ($includedFiles as $includedFilePath) {
            if (false !== strpos($includedFilePath, $templatesFilesExt)) {
                $nbIncludedTemplates++;
            } elseif (false !== strpos($includedFilePath, $packsFilesExt)) {
                $nbIncludedPacks++;
            }
        }

        return array(
            'total' => count($includedFiles),
            'templates' => $nbIncludedTemplates,
            'packs' => $nbIncludedPacks
        );
    }

    public function getDbConnectionLog($connectionName = null)
    {
        $res = array();

        $queriesLog = $this->app->get('db')->getConnection($connectionName)->getQueryLog();
        $res['nbSqlQueries'] = count($queriesLog);

        // Do we add SQL queries detail?
        if ($this->app->vars['config']['debug']['perfs.tracking.sql_queries.enabled']) {
            $res['sqlQueries'] = $queriesLog;
        }

        return $res;
    }

    /**
     * @return array
     */
    public function getAllPerfsInfo()
    {
        $perfsInfo = array();

        // Time elapsed, for different app phases
        $perfsInfo['elapsedTimeNow'] = $this->getElapsedTime();
        $perfsInfo['elapsedTimeAtBootstrap'] = $this->app->vars['perfs.bootstrap.elapsed_time'];
        $perfsInfo['elapsedTimeAtPluginsInit'] = $this->app->vars['perfs.plugins-init.elapsed_time'];
        // Number of included files, for different app phases
        $nbIncludedFilesData = $this->getNbIncludedFiles();
        $perfsInfo['nbIncludedFilesNow'] = $nbIncludedFilesData['total'];
        $perfsInfo['nbIncludedTemplatesNow'] = $nbIncludedFilesData['templates'];
        $perfsInfo['nbIncludedPacksNow'] = $nbIncludedFilesData['packs'];
        $perfsInfo['nbIncludedFilesAtBootstrap'] = $this->app->vars['perfs.bootstrap.nb_included_files'];
        $perfsInfo['nbIncludedFilesAtPluginsInit'] = $this->app->vars['perfs.plugins-init.nb_included_files'];
        // Plugins-related info
        $perfsInfo['nbPlugins'] = count($this->app->vars['plugins.registered_plugins']);
        $perfsInfo['nbPluginsPermanentlyDisabled'] = count($this->app->vars['plugins.disabled_plugins']['permanently']);
        $perfsInfo['nbPluginsDisabledForCurrentUrl'] = count($this->app->vars['plugins.disabled_plugins']['forCurrentUrl']);
        if (isset($this->app->vars['plugins.packing.duration'])) {
            $perfsInfo['pluginsPackingDuration'] = $this->app->vars['plugins.packing.duration'];
        }
        // App related info
        $perfsInfo['nbActionsRegistered'] = count($this->app->vars['plugins.actions']);
        // SQL stuff
        $defaultConnectionLog = $this->getDbConnectionLog();
        $perfsInfo['nbSqlQueries'] = $defaultConnectionLog['nbSqlQueries'];
        if (isset($defaultConnectionLog['sqlQueries'])) {
            $perfsInfo['sqlQueries'] = $defaultConnectionLog['sqlQueries'];
        }
        // Do we have a active phpBb connection?
        if (!empty($this->app->vars['phpbb.db.initialized'])) {
            // It seems we do! Let's add its SQL queries log
            $phpbbConnectionLog = $this->getDbConnectionLog($this->app->vars['phpbb.db.connection.name']);
            $perfsInfo['nbSqlQueries'] += $phpbbConnectionLog['nbSqlQueries'];
            if (isset($phpbbConnectionLog['sqlQueries'])) {
                $perfsInfo['sqlQueries'] = array_merge($perfsInfo['sqlQueries'], $phpbbConnectionLog['sqlQueries']);
            }
        }
        // View rendering duration, if any
        if (isset($this->app->vars['perfs.view.rendering.duration'])) {
            $perfsInfo['viewRenderingDuration'] = $this->app->vars['perfs.view.rendering.duration'];
        }
        // QueryPath HTML hooks duration, if any
        if (isset($this->app->vars['perfs.querypath.duration'])) {
            $perfsInfo['queryPathDuration'] = $this->app->vars['perfs.querypath.duration'];
        }

        return $perfsInfo;
    }
}
