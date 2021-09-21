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
    if(trim($dados['f_nome']) === "") {
        $erros["f_nome"] =  "O nome de usuário é obrigatório.";
    }
    if(!filter_var($dados["f_email"], FILTER_VALIDATE_EMAIL)) {
        $erros["f_email"] = "E-mail inválido.";
    }
    function validaCPF($cpf) {
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
        if (strlen($cpf) != 11) {
            return false;
        }
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
    if(!validaCPF($dados['f_cpf'])) {
        $erros["f_cpf"] =  "Insira um CPF válido.";
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
    if(trim($dados['f_nome_requerente']) === "") {
        $erros["f_nome_requerente"] =  "O nome do requerente é obrigatório.";
    }
    if(!filter_var($dados["f_email_requerente"], FILTER_VALIDATE_EMAIL)) {
        $erros["f_email_requerente"] = "E-mail inválido.";
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

        $sql = "INSERT INTO tb_pessoa 
        (cnpj_instituicao, nome_instituicao, cpf, nome, sexo, email, tipo_endereco, endereco, complemento, numero, bairro, cidade, uf, cep, tipo_perfil, nome_requerente, email_requerente, data_atualizacao) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '$now')";

        $connect = connect();
        $stmt = $connect->prepare($sql);

        $params = [
            $dados['f_cnpj'],
            $dados['f_nomeinst'],
            $dados['f_cpf'],
            $dados['f_nome'],
            $dados['f_sexo'],
            $dados['f_email'],
            $dados['f_tipo_endereco'],
            $dados['f_endereco'],
            $dados['f_complemento'],
            $dados['f_numero'],
            $dados['f_bairro'],
            $dados['f_cidade'],
            $dados['f_uf'],
            $dados['f_cep'],
            $dados['f_tipo_perfil'],
            $dados['f_nome_requerente'],
            $dados['f_email_requerente'],
        ];

        $stmt->bind_param("sssssssssssssssss", ...$params);

        if($stmt->execute()) {
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
            alert('Por favor, selecione todos os campos.');
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
    function mCPF(cpf){
        cpf=cpf.replace(/\D/g,"")
        cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")
        cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")
        cpf=cpf.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
        return cpf
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

<form action="#" method="POST" onsubmit="return checkForm()">
    <div class="form-row">
        <div class="form-group col-md-9">
            <label for="f_nomeinst">Nome da Instituição</label>
            <input type="text" id="f_nomeinst" name="f_nomeinst" maxlength="300" class="form-control <?= $erros['f_nomeinst'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_nomeinst"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_nomeinst"] ?>
            </div>
        </div>
        <div class="form-group col-md-3">
            <label for="f_cnpj">CNPJ da Instituição</label>
            <input type="text" id="f_cnpj" name="f_cnpj" onkeydown="javascript: fMasc(this, mCNPJ);" minlength="18" maxlength="18" class="form-control <?= $erros['f_cnpj'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_cnpj"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_cnpj"] ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="f_nome">Usuário</label>
            <input type="text" id="f_nome" name="f_nome" maxlength="300" class="form-control <?= $erros['f_nome'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_nome"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_nome"] ?>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="f_email">E-mail</label>
            <input type="text" id="f_email" name="f_email" maxlength="300" class="form-control <?= $erros['f_email'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_email"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_email"] ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="f_cpf">CPF</label>
            <input type="text" id="f_cpf" name="f_cpf" onkeydown="javascript: fMasc(this, mCPF);" minlength="14" maxlength="14" class="form-control <?= $erros['f_cpf'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_cpf"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_cpf"] ?>
            </div>
        </div>
        <div class="form-group col-md-4">
            <label for="f_sexo">Sexo</label>
            <select id="f_sexo" name="f_sexo" class="form-control required" value="<?= $dados["f_sexo"]; ?>">
                <option disabled selected value>Selecione</option>
                <option value="M">Masculino</option>
                <option value="F">Feminino</option>
            </select>
            <div class="invalid-feedback">
                <?= $erros["f_sexo"] ?>
            </div>
        </div>
        <div class="form-group col-md-4">
            <label for="f_tipo_endereco">Tipo de Endereço</label>
            <select id="f_tipo_endereco" name="f_tipo_endereco" class="form-control required" value="<?= $dados["f_tipo_endereco"]; ?>">
                <option disabled selected value>Selecione</option>
                <option value="1">Endereço Comercial</option>
                <option value="2">Endereço Residencial</option>
                <option value="3">Endereço Social</option>
                <option value="4">Endereço de Cobrança</option>
                <option value="5">Endereço Academico</option>
                <option value="6">Endereço das Clinicas</option>
                <option value="7">Endereço de Atendimento ao Público</option>
            </select>
            <div class="invalid-feedback">
                <?= $erros["f_tipo_endereco"] ?>
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
            <label for="f_tipo_perfil">Perfil</label>
            <select id="f_tipo_perfil" name="f_tipo_perfil" class="form-control required" value="<?= $dados["f_tipo_perfil"]; ?>">
                <option disabled selected value>Selecione</option>
                <option value="1">Analista Entidade</option>
                <option value="2">Coordenador</option>
                <option value="3">Entidade de Envio</option>
            </select>
            <div class="invalid-feedback">
                <?= $erros["f_tipo_perfil"] ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="f_nome_requerente">Nome do Requerente</label>
            <input type="text" id="f_nome_requerente" name="f_nome_requerente" maxlength="300" class="form-control <?= $erros['f_nome_requerente'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_nome_requerente"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_nome_requerente"] ?>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="f_email_requerente">E-mail do requerente</label>
            <input type="text" id="f_email_requerente" name="f_email_requerente" maxlength="300" class="form-control <?= $erros['f_email_requerente'] ? 'is-invalid' : '' ?>" value="<?= $dados["f_email_requerente"]; ?>">
            <div class="invalid-feedback">
                <?= $erros["f_email_requerente"] ?>
            </div>
        </div>
    </div>
    <button class="btn btn-primary btn-lg">Enviar</button>
</form>