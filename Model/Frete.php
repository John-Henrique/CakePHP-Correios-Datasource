<?PHP
class Frete extends AppModel {

    public $useDbConfig = 'correios';
    public $name = 'Frete';
    public $useTable = false;

    public $_schema = array(
            'Codigo'=>array('type'=>'integer','null'=>true,'length'=>11),
            'ValorMaoPropria'=>array('type'=>'boolean','null'=>true,'length'=>1),
            'ValorAvisoRecebimento'=>array('type'=>'boolean','null'=>true,'length'=>1),
            'ValorValorDeclarado'=>array('type'=>'boolean','null'=>true,'length'=>1),
            'PrazoEntrega'=>array('type'=>'integer','null'=>true,'length'=>11),
            'EntregaDomiciliar'=>array('type'=>'string','null'=>true,'length'=>1),
            'EntregaSabado'=>array('type'=>'string','null'=>true,'length'=>1),
            'Erro'=>array('type'=>'boolean','null'=>true,'length'=>1),
            'MsgErro'=>array('type'=>'string','null'=>true,'length'=>255),
            'Valor'=>array('type'=>'decimal','null'=>true,'length'=>array(10,2))
    );

} 