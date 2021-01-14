<?php

namespace main;

class DocTemplate
{
    protected $tmplName;
    protected $callback;

    private function __construct($tmplName)
    {
        $this->tmplName = $tmplName;
    }

    public static function get($tmplName)
    {
        return new self($tmplName);
    }

    public function setHandler($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    public function send($fileName)
    {
        $tbs = $this->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $fileName);
        exit;
    }

    public function prepare()
    {
        new \clsOpenTBS;
        $tbs = new \clsTinyButStrong;
        $tbs->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
        $tbs->LoadTemplate(\Yii::getAlias('@app/views/docs/' . $this->tmplName), OPENTBS_ALREADY_UTF8);
        $callback = $this->callback;
        $callback($tbs);
        $tbs->PlugIn(OPENTBS_DELETE_COMMENTS);
        return $tbs;
    }

    public function save($filePath)
    {
        $tbs = $this->prepare();
        $tbs->Show(OPENTBS_FILE, $filePath);
    }

    public function make()
    {
        $tbs = $this->prepare();
        $tbs->Source;
    }

}