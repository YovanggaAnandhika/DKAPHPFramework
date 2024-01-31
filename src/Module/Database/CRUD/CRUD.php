<?php

namespace yovanggaanandhika\dkaframework\Module\Database\CRUD;
use PDO;
use yovanggaanandhika\dkaframework\Interface\Database\CRUD as CRUDInterface;
use yovanggaanandhika\dkaframework\Module\Database\CRUD\Read\OptionsRead as Options;

class CRUD implements CRUDInterface {

    private static mixed $returnVar;
    /**
     * @return mixed
     */
    private function getReturnVar(): mixed
    {
        return self::$returnVar;
    }
    /**
     * @param mixed $returnVar
     */
    private function setReturnVar(mixed $returnVar): void
    {
        self::$returnVar = $returnVar;
    }

    private static PDO $Connector;

    private function getConnector(): PDO
    {
        return self::$Connector;
    }
    /**
     * @param mixed $Connector
     */
    private function setConnector(PDO $Connector): void
    {
        self::$Connector = $Connector;
    }

    public function __construct(PDO $Connector)
    {
        $this->setConnector($Connector);
    }

    /**
     * @param $table_name string
     * @param Options $options
     * @return array | string
     */
    public function Read($table_name = 'test', $options = new Options()) : array | string
    {
        self::setReturnVar(array(
            'status' => false,
            'code' => 500,
            'msg' => 'not initialization'
        ));

        $ExtendedScript = "";

        $reformatTableName = strval($table_name);
        $connector = $this->getConnector();
        //##############################
        $Limit = (!is_null($options::getGetLimit()) ? "LIMIT ".$options::getGetLimit() : "");
        $ExtendedScript .= $Limit;
        //#############################
        $SQLScript = /** @lang text */ "SELECT * FROM `$reformatTableName` $ExtendedScript;";
        $statement = $connector->prepare($SQLScript);
        $statement->execute();
        $fetch = $statement->fetchAll(PDO::FETCH_ASSOC);
        $errorInfo = $statement->errorInfo();
        $rowCount = $statement->rowCount();

        if (is_null($errorInfo[1])){
            if ($rowCount > 0){
                /** set Response Return Variable **/
                self::setReturnVar(array(
                    'status' => true,
                    'code' => 200,
                    'msg' => 'successfully to read data',
                    "data" => $fetch
                ));
                /** End set Response Return Variable **/
            }else{
                /** set Response Return Variable **/
                self::setReturnVar(array(
                    'status' => false,
                    'code' => 404,
                    'msg' => 'successfully, but not found data',
                    "error" => array(
                        "code" => $errorInfo[1],
                        'msg' => $errorInfo[2]
                    )
                ));
                /** End set Response Return Variable **/
            }
        }else{
            /** set Response Return Variable **/
            self::setReturnVar(array(
                'status' => false,
                'code' => 500,
                'msg' => 'fatal, error detected',
                "error" => array(
                    "code" => $errorInfo[1],
                    'msg' => $errorInfo[2]
                )
            ));
            /** End set Response Return Variable **/
        }
        /** Return Variable */
        return ($options::isJsonFormat() ? json_encode(self::getReturnVar(), true) : self::getReturnVar());
    }


}