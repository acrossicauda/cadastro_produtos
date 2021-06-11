<?php

define( 'MYSQL_HOST', 'localhost' );
define( 'MYSQL_USER', 'root' );
define( 'MYSQL_PASSWORD', '' );
define( 'MYSQL_DB_NAME', 'cad_produtos' );

class Conexao {

    private $pdo;

    private static $instance;

    protected function __construct() {
        
    }

    protected function __clone() {}

    protected function __wakeup() {}


    /**
     * Executa INSERT, UPDATE, DELETE
     * @param $query = string
     * @param $dados = array
     */
    public function execute(string $query, Array $dados = array())
    {

        $stmt = $this->pdo->prepare($query);
        if(!empty($dados)) {
            foreach($dados as $k => $v) {
                $stmt->bindParam($k, $v);
            }
        }

        $result = $stmt->execute();
        if ( !$result ) {
            return ['success' => false, 'message' => $stmt->errorInfo()];
        }
    
        return ['success' => true, 'message' => $this->pdo->lastInsertId()];
    }

    /**
     * Executa uma query, sera usado para os selects
     * @param $query = string
     */
    public function selectQuery($query = '')
    {
        try {
            $data = $this->pdo->query($query);
            return $data->fetchAll(PDO::FETCH_ASSOC);
        }
        catch ( PDOException $e ) {
            die( 'Erro ao conectar com o MySQL: ' . $e->getMessage() );
        }
        
    }

    /**
     * faz a conexao por PDO
     */
    public function conection()
    {
        try {
            $this->pdo = new PDO( 'mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        catch ( PDOException $e ) {
            echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
        }
    }

    /**
     * criando um sigleton da class pra nao ter mais de uma instancia utilizando o banco
     * o banco é instanciado por aqui tambem
     */
    public static function getInstance()
    {
        if(self::$instance === null) {
            self::$instance = new self;
            self::$instance->conection();
        } 
        return self::$instance;
    }
}