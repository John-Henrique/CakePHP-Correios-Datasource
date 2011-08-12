<?php

/**
 * Classe DataSource para CakePHP
 * 
 * Permite requisitar o cálculo de frete 
 * de alguns serviços de frete oferecidos 
 * pela ECT.
 * 
 * @author John-Henrique F. Silva
 * @name CorreioSource
 * @version 2011-08-12
 * @copyright 2011
 * 
 * @see http://www.vibemidia.com/correios-datasource-para-cakephp
 * @example https://github.com/hostdesigner/CakePHP-Correios-Datasource
 *
 */
class CorreioSource extends DataSource 
{
	
	
	/**
	 * Determina o esquema dos dados 
	 * alguns valores precisam ser revisados 
	 * o tamanho do campo não parece ser tão 
	 * grande
	 *
	 * @var Array
	 */
	public $_schema = array(
		'fretes' => array(
			
	        'nCdEmpresa' => array(
	        	'type' => 'string',
	        	'null' => true,
	        	'key' => 'primary',
	        	'length' => 10
	        ),
	        'sDsSenha' => array(
	        	'type' => 'string',
	        	'null' => true,
	        	'key' => 'primary',
	        	'length' => 10
	        ),
	        'nCdServico' => array(
	        	'type' => 'integer',
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 5
	        ),
	        'sCepOrigem' => array(
	        	'type' => 'string',
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 9
	        ),
	        'sCepDestino' => array(
	        	'type' => 'string',
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 9
	        	//78300-000
	        ),
	        'nVlPeso' => array(
	        	'type' => 'integer',
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 2
	        ),
	        'nCdFormato' => array(
	        	'type' => 'integer',
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 1
	        ),
	        'nVlComprimento' => array(
	        	'type' => 'integer',
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 4
	        ),
	        'nVlAltura' => array(
	        	'type' => 'integer',
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 4
	        ),
	        'nVlLargura' => array(
	        	'type' => 'integer',
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 4
	        ),
	        'nVlDiametro' => array(
	        	'type' => 'string', // verificar tipo de dados
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 10
	        ), 
	        'nVlValorDeclarado' => array(
	        	'type' => 'float',
	        	'null' => true,
	        	'key' => 'primary',
	        	'length' => '7,2'
	        ),
	        'sCdAvisoRecebimento' => array(
	        	'type' => 'boolean',
	        	'null' => true,
	        	'key' => 'primary',
	        	'length' => 1
	        ),
	        'sCdMaoPropria' => array(
	        	'type' => 'boolean',
	        	'null' => false,
	        	'key' => 'primary',
	        	'length' => 1
	        ),

		)
	);
	
	
	
	
	/**
	 * Seria interessante ter um método 
	 * para chamar diretamente o tipo 
	 * de frete desejado algo como
	 * $this->pac($conditions)
	 * 
	 * Não irei desenvolver isto agora
	 *
	 * @param Object $model
	 * @param Array $queryData
	 * @return Array
	 */
	public function pac( &$model, $queryData = array() )
	{
		
		/**
		 * Este é apenas um exemplo então para evitar 
		 * que alguém utilize, sempre retornará Erro 1
		 */
		return array( 'Erro' => 1 );
	}
	
	
	
	
	
	/**
	 * Este é o método padrão, neste caso podemos 
	 * requisitar apenas um tipo de frete por vez 
	 * 
	 * O mesmo que $this->find( 'all );
	 *
	 * @param Object $model
	 * @param Array $queryData
	 * @return Array com dados do frete
	 */
	public function read( &$model, $queryData = array())
	{
 		
		
		/**
		 * Facilitando o meio de campo 
		 * 
		 * Verificando se existe o tipo de serviço 
		 * e se o comprimento foi informado
		 * 
		 * Troca o nome do serviço (pac, SEDEX, SEDEXACOBRAR...) 
		 * pelo código correspondente
		 */
		if( isset( $queryData['nCdServico'] ) && isset( $queryData['nVlComprimento'] ) )
		{
			
			// Substitui o nome do serviço pelo código dele na ECT
			$queryData['nCdServico'] = $this->servicos( $queryData['nCdServico'] );
			
			
			// removendo possiveis pontos 
			$queryData = $this->ponto_virgula( $queryData );
		}
		
		
		// informando o retorno desejado
		$queryData['StrRetorno'] = 'xml';
		
		

		/**
		 * preferi XML ao invés de HttpSocket porque 
		 * facilita a manipulação 
		 * @see http://book.cakephp.org/view/935/Importing-Core-Libs
		 */
		App::import( "Core", "Xml" );
		
		
		/**
		 * Criamos a query string
		 * @see http://www.php.net/manual/en/function.http-build-query.php
		 * 
		 * decodificamos o HTML
		 * @see http://www.php.net/manual/en/function.html-entity-decode.php
		 * 
		 * criamos uma nova instancia XML 
		 * @see http://book.cakephp.org/view/1486/Xml-parsing
		 * 
		 * convertemos o XML em Array, mágica né?
		 * @see http://book.cakephp.org/view/1491/reverse
		 */
		$arrFrete = Set::reverse( new Xml( html_entity_decode( 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?' . http_build_query( $queryData ) ) ) );
		
		
		
		// se existir o indice
		if( isset( $arrFrete['Servicos']['CServico'] ) )
		{
			
			
			// total (produto + frete)
			$arrFrete['Servicos']['CServico']['total'] = ( str_replace( ',', '.', $queryData['preco'] ) + str_replace( ',', '.', $arrFrete['Servicos']['CServico']['Valor'] ) );
			
			
			// frete * quantidade
			$arrFrete['Servicos']['CServico']['Valor'] = $queryData['quantidade'] * str_replace( ',', '.', $arrFrete['Servicos']['CServico']['Valor'] );
			
			// produto * quantidade
			$queryData['preco'] = $queryData['quantidade'] + str_replace( ',', '.', $queryData['preco'] );
			
			// total * quantidade
			$arrFrete['Servicos']['CServico']['total'] = $arrFrete['Servicos']['CServico']['total'] * $queryData['quantidade'];
			
			
			return $arrFrete['Servicos']['CServico'];
		}else{
			
			return false;
		}
	}
	
	
	
	/**
	 * Lista as fontes de dados
	 *
	 * @return Array
	 */
	public function listSources()
	{
		return array('fretes');
	}
	
	
	
	/**
	 * Descrevendo os dados do modelo 
	 * Neste caso descreve o esquema de dados
	 *
	 * @param Object $model
	 * @return Objetct
	 */
	public function describe(&$model)
	{
		return $this->_schema['fretes'];
	}
	
	
	
	
	
	/**
	 * Corrige os campos altura, largura e comprimento 
	 * trocando pontos por virgulas 
	 *
	 * @param Array $arrData
	 * @return Array
	 */
	public function ponto_virgula( $arrData = array() )
	{
		
		// sempre será um array mas por via das dúvidas...
		if( is_array( $arrData ) && !empty( $arrData ) )
		{

			// percorre o indice
			foreach ( $arrData as $chave => $value)
		    {
		    	
		    	// troca , por . por questão de compartibilidade com a URL
		    	$arrData[ $chave ] = str_replace( '.', ',', $value );
		    }
		}
		
	    return $arrData;
	}
	
	
	
	/**
	 * Serviços disponíveis para cálculo 
	 * @see http://www.correios.com.br/webServices/PDF/SCPP_manual_implementacao_calculo_remoto_de_precos_e_prazos.pdf
	 *
	 * @param String $strServico informar o nome do serviço sem espaços exemplo: sedexacobrar
	 * @return Integer
	 */
	protected function servicos( $strServico = null )
	{
		
		// converte tudo para minusculo para ter compartibilidade
    	switch ( strtolower( $strServico ) )
    	{
    		case 'esedex':
    			return 81019;
    			break;
    	
    		case 'malote':
    			return 44105;
    			break;
    			
    		case 'pac':
    			return 41106;
    			break;
    			
    		case 'sedex':
    			return 40010;
    			break;
    			
    		case 'sedex10':
    			return 40215;
    			break;
    			
    		case 'sedexacobrar': 
    			return 40045;
    			break;
    			
    		case 'sedexhoje':
    			return 40290;
    			break;
    			
    		default:
    			// teremos PAC como método padrão
    			return 41106;
    			break;
    	}
	}
	
	
}

?>