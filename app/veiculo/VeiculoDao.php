<?php
namespace app\veiculo;

use \PDO as PDO;
use \Exception as Exception;

use app\util\DataBase as DataBase;

use app\veiculo\Veiculo as Veiculo;
use app\pessoa\PessoaDao as PessoaDao;

class VeiculoDao {

    public function insert(Veiculo $veiculo) {
        $sql = "
            INSERT INTO veiculo (
                placa,
                marca,
                modelo,
                pessoa_login
            ) VALUES (
                :placa,
                :marca,
                :modelo,
                :pessoa_login
            )
        ";

        $dataBase = DataBase::getInstance();
        $stmt = $dataBase->prepare($sql);
        $stmt->bindValue(':placa', $veiculo->getPlaca());
        $stmt->bindValue(':marca', $veiculo->getMarca());
        $stmt->bindValue(':modelo', $veiculo->getModelo());
        $stmt->bindValue(':pessoa_login', $veiculo->getPessoa()->getLogin());
        $stmt->execute();
        if ($stmt->rowCount() < 1) {
            throw new Exception("Can't create");
        }
    } 

    public function get(Veiculo $veiculo) {
        $sql = "
            SELECT * FROM veiculo v
            JOIN pessoa_completa pc
                ON v.pessoa_login = pc.login
            WHERE v.placa = :placa
        ";
        $dataBase = DataBase::getInstance();
        $stmt = $dataBase->prepare($sql);
        $stmt->bindValue(':placa', $veiculo->getPlaca());
        $stmt->execute();
        if ($stmt->rowCount() < 1) {
            throw new Exception("Not found", 404);
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $this->populatePessoa($result);
    }

    public function getAll() {
        $sql = "
            SELECT * FROM veiculo v
            JOIN pessoa_completa pc
                ON v.pessoa_login = pc.login
        ";
        $dataBase = DataBase::getInstance();
        $stmt = $dataBase->prepare($sql);
        $stmt->execute();
        
        if ($stmt->rowCount() < 1) {
            throw new Exception("Not found", 404);
        }
        $veiculos = [];
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($result as $data) {
            $veiculos[] = $this->populatePessoa($data);
        }
        return $veiculos;


    }

    public function update(Veiculo $veiculo) {
        $sql = "
            UPDATE veiculo SET 
                marca = :marca,
                modelo = :modelo,
                pessoa_login = :pessoa_login
            WHERE placa = :placa
        ";

        $dataBase = DataBase::getInstance();
        $stmt = $dataBase->prepare($sql);
        $stmt = $this->bindValues($stmt, $pessoa);                
        $stmt->execute();
        $pessoa = $this->get($pessoa);
        return $pessoa;
    }

    public function delete(Veiculo $veiculo) {
        $sql = "
            DELETE FROM veiculo
            WHERE placa = :placa
        ";
        $dataBase = DataBase::getInstance();
        $stmt = $dataBase->prepare($sql);
        $stmt->bindValue(':placa', $veiculo->getPlaca());
        $stmt->execute();
        if ($stmt->rowCount() < 1) {
            throw new Exception("Not found", 404);
        }
    }

    public function populateVeiculo($data) {
        $veiculo = new Veiculo();
        $veiculo->setPlaca($data['placa']);
        $veiculo->setMarca($data['marca']);
        $veiculo->setModelo($data['modelo']);

        $pessoaDao = new PessoaDao();
        $pessoa = $pessoaDao->populatePessoa($data);

        $veiculo->setPessoa($pessoa);
    }   

}