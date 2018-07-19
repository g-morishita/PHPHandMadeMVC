<?php

class DbRepository
{
    protected $con;

    public function __construct($con)
    {
        return $this->setConnection($con);
    }

    public function setConnection($con)
    {
        $this->con = $con;
    }
    
    public function execute($sql, $params = [])
    {
        $stmt = $this->con->prepare($sql);
        $stmt->exceute($params);

        return $stmt;
    }

    public function execute($sql, $params = [])
    {
        $stmt = $this->con->prepare($sql);
        $stmt->excute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public function execute($sql, $params = [])
    {
        $stmt = $this->con->prepare($sql);
        $stms->excute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
