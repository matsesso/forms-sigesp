<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<?php
if(count($_POST) > 0) {
    $dados = $_POST;
    $erros = [];

    if(trim($dados['f_nomeinst']) === "") {
        $erros["f_nomeinst"] =  "O nome da Instituição é obrigatório.";
    }
    function validaCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        if (strlen($cnpj) != 14)
            return false;
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;	
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
    if(!validaCNPJ($dados['f_cnpj'])) {
        $erros["f_cnpj"] =  "Insira um CNPJ válido.";
    }
    if(trim($dados['f_telefone']) === "") {
        $erros["f_telefone"] =  "O telefone é obrigatório.";
    }
    if(!filter_var($dados["f_email"], FILTER_VALIDATE_EMAIL)) {
        $erros["f_email"] = "E-mail inválido.";
    }
    if(trim($dados['f_endereco']) === "") {
        $erros["f_endereco"] =  "O endereço é obrigatório.";
    }
    if(trim($dados['f_numero']) === "") {
        $erros["f_numero"] =  "O número é obrigatório.";
    }
    if(trim($dados['f_complemento']) === "") {
        $erros["f_complemento"] =  "O bairro é obrigatório.";
    }
    if(trim($dados['f_bairro']) === "") {
        $erros["f_bairro"] =  "O bairro é obrigatório.";
    }
    if(trim($dados['f_cidade']) === "") {
        $erros["f_cidade"] =  "A cidade é obrigatória.";
    }
    if(trim($dados['f_cep']) === "") {
        $erros["f_cep"] =  "O CEP é obrigatório.";
    }

    $file = explode('.', $_FILES['f_doc']['name']);
    $file_size = $_FILES['f_doc']['size'];

    if(($file_size > 10485760) && $file[sizeof($file)-1] != 'pdf') {   
        $erros["f_doc"] =  "Permitido somente aquivo do tipo PDF até 10MB.";
    }
    if(!count($erros)) {
        function connect() {
            $server = 'localhost';
            $user = '';
            $pass = '';
            $database = '';
            $conexao = new mysqli($server, $user, $pass, $database);
        
            if($conexao->connect_error) {
                die('Error: ' . $conexao->connect_error);
            }
            return $conexao;
        }

        $now = date('d/m/Y H:i');

        $filename = uniqid() . "-" . date('dmY');
        $extension = pathinfo($_FILES["f_doc"]["name"], PATHINFO_EXTENSION);
        $basename = $filename . "." . $extension;
    
        $source = $_FILES['f_doc']['tmp_name'];
        $destination = "/var/www/html/website/wp-content/{$basename}";
        $linkArquivo =  "https://" . $_SERVER['HTTP_HOST'] . "/wp-content/{$basename}";

        $sql = "INSERT INTO tb_pessoa_juridica 
        (cnpj, razao_social, email, telefone, endereco, complemento, numero, bairro, cidade, uf, cep, diretorio_documento, data_atualizacao) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '$linkArquivo', '$now')";

        $connect = connect();
        $stmt = $connect->prepare($sql);

        $params = [
            $dados['f_cnpj'],
            $dados['f_nomeinst'],
            $dados['f_email'],
            $dados['f_telefone'],
            $dados['f_endereco'],
            $dados['f_complemento'],
            $dados['f_numero'],
            $dados['f_bairro'],
            $dados['f_cidade'],
            $dados['f_uf'],
            $dados['f_cep'],
        ];

        $stmt->bind_param("sssssssssss", ...$params);

        if($stmt->execute()) {
            if(!empty($_FILES['f_doc'])) {       
              move_uploaded_file($source, $destination);
            }
            unset($dados);
            echo "<script>alert('Os dados foram enviados com sucesso!');</script>";
        } else {
            echo "Ocorreu um erro: " . $connect->error;
        }
    }
}
?>

<script type="text/javascript">
    function checkForm() {
        var inputs = document.getElementsByClassName('required');
        var len = inputs.length;
        var valid = true;
        for(var i=0; i < len; i++) {
            if (!inputs[i].value) { 
                valid = false; 
            }
        }
        if (!valid) {
            alert('Por favor, preencha todos os campos.');
            return false;
        } else { 
            return true;
        }
    }
    function fMasc(objeto, mascara) {
        obj=objeto
        masc=mascara
        setTimeout("fMascEx()",1)
    }
    function fMascEx() {
        obj.value=masc(obj.value)
    }
    function mTel(tel) {
        tel=tel.replace(/\D/g,"")
        tel=tel.replace(/^(\d)/,"($1")
        tel=tel.replace(/(.{3})(\d)/,"$1)$2")
        if(tel.length == 9) {
            tel=tel.replace(/(.{1})$/,"-$1")
        } else if (tel.length == 10) {
            tel=tel.replace(/(.{2})$/,"-$1")
        } else if (tel.length == 11) {
            tel=tel.replace(/(.{3})$/,"-$1")
        } else if (tel.length == 12) {
            tel=tel.replace(/(.{4})$/,"-$1")
        } else if (tel.length > 12) {
            tel=tel.replace(/(.{4})$/,"-$1")
        }
        return tel;
    }
    function mCNPJ(cnpj){
        cnpj=cnpj.replace(/\D/g,"")
        cnpj=cnpj.replace(/^(\d{2})(\d)/,"$1.$2")
        cnpj=cnpj.replace(/^(\d{2})\.(\d{3})(\d)/,"$1.$2.$3")
        cnpj=cnpj.replace(/\.(\d{3})(\d)/,".$1/$2")
        cnpj=cnpj.replace(/(\d{4})(\d)/,"$1-$2")
        return cnpj
    }
    function mCEP(cep){
        cep=cep.replace(/\D/g,"")
        cep=cep.replace(/^(\d{2})(\d)/,"$1.$2")
        cep=cep.replace(/\.(\d{3})(\d)/,".$1-$2")
        return cep
    }
</script>

<form action="#" method="POST" enctype="multipart/form-data" onsubmit="return checkForm()">
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="f_cnpj">CNPJ</label>
            <input type="text" id="f_cnpj" name="f_cnpj" onkeydown="javascript: fMasc(this, mCNPJ);" minlength="18" maxlength="18" class="form-control <?= $erros['f_cnpj'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_cnpj"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_cnpj"] ?>
            </div>
        </div>
        <div class="form-group col-md-8">
            <label for="f_nomeinst">Razao Social</label>
            <input type="text" id="f_nomeinst" name="f_nomeinst" maxlength="300" class="form-control <?= $erros['f_nomeinst'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_nomeinst"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_nomeinst"] ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-8">
            <label for="f_email">E-mail</label>
            <input type="text" id="f_email" name="f_email" maxlength="150" class="form-control <?= $erros['f_email'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_email"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_email"] ?>
            </div>
        </div>
        <div class="form-group col-md-4">
            <label for="f_telefone">Telefone</label>
            <input type="text" id="f_telefone" name="f_telefone" onkeydown="javascript: fMasc(this, mTel);" minlength="13" maxlength="14" class="form-control <?= $erros['f_telefone'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_telefone"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_telefone"] ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-9">
            <label for="f_endereco">Endereço</label>
            <input type="text" id="f_endereco" name="f_endereco" maxlength="100" class="form-control <?= $erros['f_endereco'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_endereco"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_endereco"] ?>
            </div>
        </div>
        <div class="form-group col-md-3">
            <label for="f_numero">Número</label>
            <input type="text" id="f_numero" name="f_numero" maxlength="10" class="form-control <?= $erros['f_numero'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_numero"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_numero"] ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="f_bairro">Bairro</label>
            <input type="text" id="f_bairro" name="f_bairro" maxlength="50" class="form-control <?= $erros['f_bairro'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_bairro"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_bairro"] ?>
            </div>
        </div>
        <div class="form-group col-md-3">
            <label for="f_cidade">Cidade</label>
            <input type="text" id="f_cidade" name="f_cidade" maxlength="50" class="form-control <?= $erros['f_cidade'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_cidade"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_cidade"] ?>
            </div>
        </div>
        <div class="form-group col-md-3">
            <label for="f_uf">Estado</label>
            <select id="f_uf" name="f_uf" class="form-control required" value="<?= $dados["f_uf"]; ?>">
                <option disabled selected value>Selecione</option>
                <option value="AC">Acre</option>
                <option value="AL">Alagoas</option>
                <option value="AM">Amazonas</option>
                <option value="AP">Amapá</option>
                <option value="BA">Bahia</option>
                <option value="CE">Ceará</option>
                <option value="DF">Distrito Federal</option>
                <option value="ES">Espírito Santo</option>
                <option value="GO">Goiás</option>
                <option value="MA">Maranhão</option>
                <option value="MT">Mato Grosso</option>
                <option value="MS">Mato Grosso do Sul</option>
                <option value="MG">Minas Gerais</option>
                <option value="PA">Pará</option>
                <option value="PB">Paraíba</option>
                <option value="PR">Paraná</option>
                <option value="PE">Pernambuco</option>
                <option value="PI">Piauí</option>
                <option value="RJ">Rio de Janeiro</option>
                <option value="RN">Rio Grande do Norte</option>
                <option value="RO">Rondônia</option>
                <option value="RS">Rio Grande do Sul</option>
                <option value="RR">Roraima</option>
                <option value="SC">Santa Catarina</option>
                <option value="SE">Sergipe</option>
                <option value="SP">São Paulo</option>
                <option value="TO">Tocantins</option>
            </select>
            <div class="invalid-feedback">
                <?= $erros["f_uf"] ?>
            </div>
        </div>
        <div class="form-group col-md-3">
            <label for="f_cep">CEP</label>
            <input type="text" id="f_cep" name="f_cep" onkeydown="javascript: fMasc(this, mCEP);" minlength="10" maxlength="10" class="form-control <?= $erros['f_cep'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_cep"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_cep"] ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="f_complemento">Complemento</label>
            <input type="text" id="f_complemento" name="f_complemento" maxlength="70" class="form-control <?= $erros['f_complemento'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_complemento"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_complemento"] ?>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="f_doc">Arquivo</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
            <input type="file" id="f_doc" name="f_doc" accept=".pdf" class="form-control required <?= $erros['f_doc'] ? 'is-invalid' : '' ?>" />
            <div class="invalid-feedback">
                <?= $erros["f_doc"] ?>
            </div>
        </div>
    </div>
    <button class="btn btn-primary btn-lg">Enviar</button>
</form>