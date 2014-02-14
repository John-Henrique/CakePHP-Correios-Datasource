CakePHP-Correios
================

Plugin para fazer Cálculo de Frete nos Correios. Podendo ser utilizado via Datasource, ou via Component.

Como Usar?
----------

É necessário adicionar o Plugin no bootstrap.php do projeto, incluindo o bootstrap do plugin.

        CakePlugin::load('Correios',array('bootstrap'=>true));

Adicionar o Datasource no database.php do projeto

        public $correios = array('datasource' => 'Correios.CorreiosSource');

> Caso queira utilizar via Datasource, é necessário definir a Model no $uses.

        public $uses = array('Correios.Frete');

> Os dados devem ser passados via conditions. Altura (altura), Comprimento (comprimento), Largura (largura), Peso (peso), Cep de Origem (ceporigem), Cep de Destino (cep), Serviços (servicos). O caso abaixo passa apenas o SEDEX como Serviço:

        $return = $this->Frete->find('all',array('conditions'=>array(
                    'altura'=>'10',
                    'comprimento'=>20,
                    'largura'=>20,
                    'peso'=>10,
                    'servicos'=>'SEDEX',
                    'ceporigem'=>'85802-200',
                    'cep'=>'27511300'
                  )));

> Pode ser passado mais de um serviço:

        $return = $this->Frete->find('all',array('conditions'=>array(
                    'altura'=>'10',
                    'comprimento'=>20,
                    'largura'=>20,
                    'peso'=>10,
                    'servicos'=>array('PAC','SEDEX','SEDEX_10'),
                    'ceporigem'=>'85802-200',
                    'cep'=>'27511300'
                  )));

> A segunda opção é utilizando Component. É necessário definir o Component no $components.

        public $components = array('Correios.Correios');

> E pegar os dados dessa maneira:

        $return = $this->Correios->get(10,20,20,10,'85802200','27511300',array('PAC','SEDEX'));
        // Parâmetros: Altura, Comprimento, Largura, Peso, Cep de Origem, Cep de Destino, Serviços (Um ou mais)

Os Serviços que podem ser consultados são esses:

           NORMAL -> Normal
           SEDEX -> Sedex sem contrato
           SEDEX_HOJE -> Sedex Hoje, sem contrato
           E_SEDEX -> e-Sedex, com contrato
           SEDEX_10 -> Sedex 10, sem contrato
           SEDEX_A_COBRAR -> Sedex À Cobrar, sem contrato
           SEDEX_CONTRATO_1 -> Sedex com contrato
           SEDEX_CONTRATO_2 -> Sedex com contrato
           SEDEX_CONTRATO_3 -> Sedex com contrato
           PAC -> PAC sem contrato
           PAC_CONTRATO -> PAC com contrato
           MALOTE -> Malote


Créditos
--------

João Batista Neto (iMasters) https://github.com/netojoaobatista, pelo desenvolvimento do Pacote de Integração com os Correios.
Sobre o pacote: https://github.com/iMastersDev/correios