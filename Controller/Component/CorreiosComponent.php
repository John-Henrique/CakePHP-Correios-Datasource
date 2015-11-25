<?PHP
class CorreiosComponent extends Component {

    private $ect;

    private $prdt;

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

    public function __construct(ComponentCollection $collection, $settings = array()){
        parent::__construct($collection, $settings);
        $this->ect=new ECT();
        $this->prdt=$this->ect->prdt();
    }

    public function get($Altura,$Comprimento,$Largura,$Peso,$CepOrigem,$CepDestino,$Servicos=ECTServicos::PAC,$Formato=ECTFormatos::FORMATO_CAIXA_PACOTE){
        $this->prdt->setNVlAltura( $Altura );
        $this->prdt->setNVlComprimento( $Comprimento );
        $this->prdt->setNVlLargura( $Largura );
        $this->prdt->setNCdFormato( $Formato );
        if(is_array($Servicos)){
            foreach($Servicos as $k => $Servico){ $Servicos[$k]=$this->services[$Servico]; } // Set Services code
            $this->prdt->setNCdServico( implode( ',' , $Servicos ) );
        }else {
            $this->prdt->setNCdServico($this->services[$Servicos]); // Set service code
        }
        $this->prdt->setSCepOrigem( $this->_fixCep($CepOrigem) );
        $this->prdt->setSCepDestino( $this->_fixCep($CepDestino) );
        $this->prdt->setNVlPeso( $Peso );
        $return = $this->prdt->call();
        $return = @$this->_toArray(iterator_to_array($return,true));
        return $this->_fixReturn($return);
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