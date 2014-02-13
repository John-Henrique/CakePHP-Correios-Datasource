<?PHP
class CorreiosSource extends DataSource {

    private $ect;
    private $prdt;
    // DEFAULT FORMAT
    public $format = ECTFormatos::FORMATO_CAIXA_PACOTE;

    /**
     * @var array
     * Define services for search
     */

    public $services = array(
        'NORMAL'=>ECTServicos::NORMAL,
        'SEDEX'=>ECTServicos::SEDEX,
        'SEDEX_HOJE'=>ECTServicos::SEDEX_HOJE,
        'E_SEDEX'=>ECTServicos::E_SEDEX,
        'SEDEX_10'=>ECTServicos::SEDEX_10,
        'SEDEX_A_COBRAR'=>ECTServicos::SEDEX_A_COBRAR,
        'SEDEX_CONTRATO_1'=>ECTServicos::SEDEX_CONTRATO_1,
        'SEDEX_CONTRATO_2'=>ECTServicos::SEDEX_CONTRATO_2,
        'SEDEX_CONTRATO_3'=>ECTServicos::SEDEX_CONTRATO_3,
        'PAC'=>ECTServicos::PAC,
        'PAC_CONTRATO'=>ECTServicos::PAC_CONTRATO,
        'MALOTE'=>ECTServicos::MALOTE,
    );



    /**
     * listSources method required by Cake
     * @param null $data
     * @return array|null
     */
    public function listSources($data = null){
        return null;
    }


    public function __construct($config = array()){
        parent::__construct($config);
        $this->ect = new ECT();
        $this->prdt= $this->ect->prdt();
    }

    public function read(Model $model, $queryData = array(), $recursive = null){
        parent::read($model, $queryData, $recursive);

        $services = array();
        if(!empty($queryData['conditions']['servicos'])){
            if(is_array($queryData['conditions']['servicos'])){
                foreach($queryData['conditions']['servicos'] as $servico){
                    $services[]=$this->services[$servico];
                }
            }else{
                $services=$this->services[$queryData['conditions']['servicos']];
            }
        }else

        // SET DEFAULT FORMAT
        $this->format = ECTFormatos::FORMATO_CAIXA_PACOTE;
        if(!empty($queryData['conditions']['formato'])){ $this->format=$queryData['conditions']['formato']; }

        // GET VALUES
        $return = $this->_getECTValues($queryData['conditions']['altura'],$queryData['conditions']['comprimento'],$queryData['conditions']['largura'],$queryData['conditions']['peso'],$queryData['conditions']['ceporigem'],$queryData['conditions']['cep'],$services,$this->format);
        // CONVERTS ARRAY ITERATOR TO ARRAY
        $return = iterator_to_array($return,true);
        // CONVERT ARRAY OBJECTS TO ARRAY
        $return = $this->_toArray($return);
        // INSERT MODEL NAME AND RETURNS
        return $this->_fixReturn($return);

    }

    private function _getECTValues($Altura,$Comprimento,$Largura,$Peso,$CepOrigem,$CepDestino,$Servicos=ECTServicos::PAC,$Formato){
        $this->prdt->setNVlAltura( $Altura );
        $this->prdt->setNVlComprimento( $Comprimento );
        $this->prdt->setNVlLargura( $Largura );
        $this->prdt->setNCdFormato( $Formato );
        if(is_array($Servicos)){
            $this->prdt->setNCdServico( implode( ',' , $Servicos ) );
        }else {
            $this->prdt->setNCdServico($Servicos);
        }
        $this->prdt->setSCepOrigem( $this->_fixCep($CepOrigem) );
        $this->prdt->setSCepDestino( $this->_fixCep($CepDestino) );
        $this->prdt->setNVlPeso( $Peso );
        return $this->prdt->call();
    }

    /**
     * Method for fix cep removing dots and dashes
     */
    private function _fixCep($cep){
        $cep=str_replace('-','',$cep);
        $cep=str_replace('.','',$cep);
        return $cep;
    }

    /**
     * Parse array inserting model name
     */
    private function _fixReturn($data,$alias='Frete'){
        $return = array();
        foreach($data as $k => $service){ $return[$k][$alias] = $service; }
        return $return;
    }

    /**
     * Method to convert object array to array
     */
    private function _toArray($stdData){
        if(is_object($stdData)) $stdData = get_object_vars($stdData);
        if(is_array($stdData)) return array_map(array($this,__FUNCTION__),$stdData);
        return $stdData;
    }
}