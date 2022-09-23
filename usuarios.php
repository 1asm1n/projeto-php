<?php
include('conexao.php');
Class Usuario
{
    private $pdo; //pdo é uma variável privada, conhecida como uma extensão do php para acessar o banco de dados
    public $msgErro = ""; //msgErro começa vazia porque ela só vai ser tratada em caso de erro

    public function conectar($nome, $host, $usuario, $senha)//conectar é um método
    {
        global $pdo; //precisa do global para ele conseguir acessar a pdo
        try  //o try é importante porque caso dê erro, ele já vai ser tratado logo em seguida, no catch
        {
            $pdo = new PDO("mysql:dbname=".$nome.";host=".$host,$usuario,$senha); //instanciação do objeto pdo na classe com o método construtor PDO
        } catch (PDOException $e) {
            global $msgErro;
            $msgErro = $e->getMessage();
        }
        // os pontos representam a concatenação do objeto com seus respectivos nomes 
    }


    public function cadastrar($nome, $telefone, $email, $senha)//cadastrar é um método
    {
        global $pdo;
        // verificar se ja existe um email cadastrado
        $sql = $pdo->prepare("SELECT id_usuario FROM usuarios
         WHERE email = :e");  
        //para enviar um comando sql pelo pdo, criamos a variavel (sql) e usamos o método prepare
        //o select foi utilizado para buscar pelos emails, a variável "e"será substituída pelo email dos usuarios
        $sql->bindValue(":e", $email); //o bindValue é o método que substitui a variavel pelo email do usuario (para buscar por todos)
        $sql ->execute(); //método para executar a "conferência""
        if($sql -> rowCount() > 0)
        //o método rowCount conta as linhas do banco, se ele for maior que zero, significa que a pessoa já está cadastrada
        {
            return false;  //a pessoa já está cadastrada
        }
        else{
            //caso nao, cadastrar
            //a seta é o "recebe"
            $sql -> $pdo -> prepare("INSERT INTO usuarios(nome, telefone, email, senha) VALUES (:n, :t, :e, :s");
            //inserimos com o prepare os atributos dos usuarios e passamos eles pelos parametros n,t,e e s, que serão alterados com o bindValue
            $sql->bindValue(":n", $nome);
            $sql->bindValue(":t", $telefone);
            $sql->bindValue(":e", $email);
            $sql->bindValue(":s", md5($senha)); //md5 embaralha e criptografa as senhas
            $sql->execute(); //executando a "alteração"de valores e dando nome aos bois
            return true; //a pessoa foi cadastrada com sucesso.
        }
    }


    public function logar($email, $senha)//logar é um método
    {
        global $pdo;
        
        //verificar se o email e senha ja estao cadasrados
        $sql = $pdo -> prepare("SELECT id_usuario FROM usuarios WHERE email = :e AND senha = :s");
        $sql -> binValue(":e", $email);
        $sql -> binValue(":s", md5($senha));
        $sql -> execute();

        if($sql->rowCount() > 0)
        //se sim, entrar no sistema, uma sessão apenas para pessoas cadastradas que vamos criar
        {
         $dado = $sql -> fetch(); //o fetch pega tudo que veio do bd e transforma em um array
         session_start(); //iniciando a sessão
         $_SESSION['id_usuario'] = $dado['id_usuario']; //armazenando o id do usuario recém logado na sessão
         return true; //logado com sucesso  
        }

        else{
            return false; //a pessoa não foi encontrada no bd e não foi logada
        }
 }
}
