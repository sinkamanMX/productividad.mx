<?php
class My_Controller_Htmlmailing
{
	public $realPath='/var/www/vhosts/sima/htdocs/public';
	//public $realPath='/Users/itecno2/Documents/workspace/productividad.mx/public';
	
	public function newSolicitud($dataSol, $dataUser){
		$cMailing   = new My_Model_Mailing();
		ob_start();
		include($this->realPath.'/layouts/mail/tSolicitud_nueva.html');
		$lBody = ob_get_clean();
		
		$sDireccion = $dataSol['CALLE'].", Entre Calles:".$dataSol['ENTRE_CALLES'].", ".$dataSol['COLONIA'].", ".$dataSol['MUNICIPIO'].", ".$dataSol['ESTADO'].", CP: ".$dataSol['CP']."</br>".
					  "Referencias: ".$dataSol['REFERENCIAS'].", Contacto:".$dataSol['CONTACTO'].", Tel. Contacto:".$dataSol['CONTACTO_TEL'];
							   
		$lBody = str_ireplace('@_usuario_@', 	@$dataUser['N_USER']   , $lBody);
		$lBody = str_ireplace('@_empresa_@', 	$dataUser['N_EMPRESA'] , $lBody);
		$lBody = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBody);
		$lBody = str_ireplace('@_tipequipo_@', 	$dataSol['N_EQUIPO']   , $lBody);
		$lBody = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBody);
		$lBody = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO'] , $lBody);
		$lBody = str_ireplace('@_lugarservicio_@',$sDireccion , $lBody);
		$lBody = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'] , $lBody);
		$lBody = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBody);
						
		$aMailerUda    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => @$dataUser['N_USER'],
			'inputEmails' 		 => @$dataUser['EMAIL'],
			'inputTittle' 		 => 'Solicitud de Servicio',
			'inputBody' 		 => $lBody,
			'inputLiveNotif'	 => 1,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
							
		$cMailing->insertRow($aMailerUda);	
		
		ob_start();
		include($this->realPath.'/layouts/mail/tempIntNuevo.html');
		$lBodyUda = ob_get_clean();	
		
		$sMensaje = 'ha realizado una solicitud de cita en el sistema de Siames';

		$lBodyUda = str_ireplace('@_usuario_@', 	@$dataUser['N_USER']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_empresa_@', 	$dataUser['N_EMPRESA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBodyUda);
		$lBodyUda = str_ireplace('@_tipequipo_@', 	$dataSol['N_EQUIPO']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO']  , $lBodyUda);
		$lBodyUda = str_ireplace('@_lugarservicio_@',$sDireccion , $lBodyUda);
		$lBodyUda = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'], $lBodyUda);
		$lBodyUda = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_mensaje_@',     $sMensaje ,                $lBodyUda);		

		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$aDataAdmin = $config->getOption('admin');					
		
		$aMailer    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => $aDataAdmin['mails'],
			'inputEmails' 		 => $aDataAdmin['mails'],
			'inputTittle' 		 => 'Solicitud de Servicio',
			'inputBody' 		 => $lBodyUda,
			'inputLiveNotif'	 => 0,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
													
		$cMailing->insertRow($aMailer);	

		if($dataSol['ID_TIPO']==3){
			$aDataRev = $config->getOption('mrevision');
			$aMailerR    = Array(
				'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
				'inputDestinatarios' => $aDataRev['mails'],
				'inputEmails' 		 => $aDataRev['mails'],
				'inputTittle' 		 => 'Solicitud de Servicio',
				'inputBody' 		 => $lBodyUda,
				'inputLiveNotif'	 => 0,
				'inputFromName' 	 => 'contacto@grupouda.com.mx',
				'inputFromEmail' 	 => 'Siames - Grupo UDA'
			);
													
			$cMailing->insertRow($aMailerR);	
		}
	}
	
	public function changeSolicitud($dataSol, $dataUser){
		$cMailing   = new My_Model_Mailing();		
		ob_start();
		include($this->realPath.'/layouts/mail/tempIntNuevo.html');
		$lBodyUda = ob_get_clean();	
		
		$sMensaje = 'Cambio de fecha y horario de instalacion';
		$sDireccion = $dataSol['CALLE'].", Entre Calles:".$dataSol['ENTRE_CALLES'].", ".$dataSol['COLONIA'].", ".$dataSol['MUNICIPIO'].", ".$dataSol['ESTADO'].", CP: ".$dataSol['CP']."</br>".
					  "Referencias: ".$dataSol['REFERENCIAS'].", Contacto:".$dataSol['CONTACTO'].", Tel. Contacto:".$dataSol['CONTACTO_TEL'];
		
		$lBodyUda = str_ireplace('@_usuario_@', 	@$dataUser['N_USER']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_empresa_@', 	$dataUser['N_EMPRESA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBodyUda);
		$lBodyUda = str_ireplace('@_tipequipo_@', 	$dataSol['N_EQUIPO']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO']  , $lBodyUda);
		$lBodyUda = str_ireplace('@_lugarservicio_@',$sDireccion , $lBodyUda);
		$lBodyUda = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'], $lBodyUda);
		$lBodyUda = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_mensaje_@',     $sMensaje ,                $lBodyUda);		
			
		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$aDataAdmin = $config->getOption('admin');					
		
		$aMailer    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => $aDataAdmin['mails'],
			'inputEmails' 		 => $aDataAdmin['mails'],
			'inputTittle' 		 => 'Cambio en la Solicitud',
			'inputBody' 		 => $lBodyUda,
			'inputLiveNotif'	 => 0,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
													
		$cMailing->insertRow($aMailer);		
	}
	
	public function acceptuserSolicitud($dataSol, $dataUser){
		$cMailing   = new My_Model_Mailing();
		ob_start();
		include($this->realPath.'/layouts/mail/tSolicitd_aceptada.html');
		$lBody = ob_get_clean();
		
		$sDireccion = $dataSol['CALLE'].", Entre Calles:".$dataSol['ENTRE_CALLES'].", ".$dataSol['COLONIA'].", ".$dataSol['MUNICIPIO'].", ".$dataSol['ESTADO'].", CP: ".$dataSol['CP']."</br>".
					  "Referencias: ".$dataSol['REFERENCIAS'].", Contacto:".$dataSol['CONTACTO'].", Tel. Contacto:".$dataSol['CONTACTO_TEL'];
							   
		$lBody = str_ireplace('@_usuario_@', 	@$dataUser['N_USER']   , $lBody);
		$lBody = str_ireplace('@_empresa_@', 	$dataUser['N_EMPRESA'] , $lBody);
		$lBody = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBody);
		$lBody = str_ireplace('@_tipequipo_@', 	$dataSol['N_EQUIPO']   , $lBody);
		$lBody = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBody);
		$lBody = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO'] , $lBody);
		$lBody = str_ireplace('@_lugarservicio_@',$sDireccion , $lBody);
		$lBody = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'] , $lBody);
		$lBody = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBody);
						
		$aMailerUda    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => @$dataUser['N_USER'],
			'inputEmails' 		 => @$dataUser['EMAIL'],
			'inputTittle' 		 => 'Solicitud de Servicio',
			'inputBody' 		 => $lBody,
			'inputLiveNotif'	 => 1,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
							
		$cMailing->insertRow($aMailerUda);	
		
		ob_start();
		include($this->realPath.'/layouts/mail/tempIntNuevo.html');
		$lBodyUda = ob_get_clean();	
		
		$sMensaje = 'acepto la solicitud de cita';

		$lBodyUda = str_ireplace('@_usuario_@', 	@$dataUser['N_USER']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_empresa_@', 	$dataUser['N_EMPRESA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBodyUda);
		$lBodyUda = str_ireplace('@_tipequipo_@', 	$dataSol['N_EQUIPO']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO']  , $lBodyUda);
		$lBodyUda = str_ireplace('@_lugarservicio_@',$sDireccion , $lBodyUda);
		$lBodyUda = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'], $lBodyUda);
		$lBodyUda = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_mensaje_@',     $sMensaje ,                $lBodyUda);		

		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$aDataAdmin = $config->getOption('admin');					
		
		$aMailer    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => $aDataAdmin['mails'],
			'inputEmails' 		 => $aDataAdmin['mails'],
			'inputTittle' 		 => 'Solicitud de Servicio',
			'inputBody' 		 => $lBodyUda,
			'inputLiveNotif'	 => 0,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
													
		$cMailing->insertRow($aMailer);
		
		if($dataSol['ID_TIPO']==3){
			$aDataRev = $config->getOption('mrevision');
			$aMailerR    = Array(
				'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
				'inputDestinatarios' => $aDataRev['mails'],
				'inputEmails' 		 => $aDataRev['mails'],
				'inputTittle' 		 => 'Solicitud de Servicio',
				'inputBody' 		 => $lBodyUda,
				'inputLiveNotif'	 => 0,
				'inputFromName' 	 => 'contacto@grupouda.com.mx',
				'inputFromEmail' 	 => 'Siames - Grupo UDA'
			);
													
			$cMailing->insertRow($aMailerR);	
		}		
	}
	
	public function acceptuserExternalSolicitud($dataSol, $dataUser){
		$cMailing   = new My_Model_Mailing();
		ob_start();
		include($this->realPath.'/layouts/mail/tSolicitd_aceptada_ext.html');
		$lBody = ob_get_clean();
		
		$lBody = str_ireplace('@_usuario_@', 	@$dataUser['N_USER']   , $lBody);
		$lBody = str_ireplace('@_empresa_@', 	$dataUser['N_CLIENTE'] , $lBody);
		$lBody = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBody);
		$lBody = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBody);
		$lBody = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO'] , $lBody);
		$lBody = str_ireplace('@_horario2_@', 	$dataSol['N_HORARIO2']  , $lBody);		
		$lBody = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'] , $lBody);
		$lBody = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBody);
						
		$aMailerUda    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => @$dataUser['N_USER'],
			'inputEmails' 		 => @$dataUser['EMAIL'],
			'inputTittle' 		 => 'Solicitud de Servicio',
			'inputBody' 		 => $lBody,
			'inputLiveNotif'	 => 1,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
							
		$cMailing->insertRow($aMailerUda);	
		
		ob_start();
		include($this->realPath.'/layouts/mail/tempIntNuevo.html');
		$lBodyUda = ob_get_clean();	
		
		$sMensaje = 'acepto la solicitud de cita';

		$lBodyUda = str_ireplace('@_usuario_@', 	@$dataUser['N_USER']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_empresa_@', 	$dataUser['N_CLIENTE'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBodyUda);
		$lBodyUda = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO']  , $lBodyUda);
		$lBodyUda = str_ireplace('@_horario2_@', 	$dataSol['N_HORARIO2']  , $lBodyUda);
		$lBodyUda = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'], $lBodyUda);
		$lBodyUda = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_mensaje_@',     $sMensaje ,                $lBodyUda);		

		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$aDataAdmin = $config->getOption('admin');					
		
		$aMailer    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => $aDataAdmin['mails'],
			'inputEmails' 		 => $aDataAdmin['mails'],
			'inputTittle' 		 => 'Solicitud de Servicio',
			'inputBody' 		 => $lBodyUda,
			'inputLiveNotif'	 => 0,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
													
		$cMailing->insertRow($aMailer);
		
		if($dataSol['ID_TIPO']==3){
			$aDataRev = $config->getOption('mrevision');
			$aMailerR    = Array(
				'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
				'inputDestinatarios' => $aDataRev['mails'],
				'inputEmails' 		 => $aDataRev['mails'],
				'inputTittle' 		 => 'Solicitud de Servicio',
				'inputBody' 		 => $lBodyUda,
				'inputLiveNotif'	 => 0,
				'inputFromName' 	 => 'contacto@grupouda.com.mx',
				'inputFromEmail' 	 => 'Siames - Grupo UDA'
			);
													
			$cMailing->insertRow($aMailerR);	
		}		
	}
	
	public function changeSolicitudExt($dataSol, $dataUser){
		$cMailing   = new My_Model_Mailing();		
		ob_start();
		include($this->realPath.'/layouts/mail/tSolicitud_cambio_ext.html');
		$lBodyUda = ob_get_clean();	
		
		$dataSol['N_HORARIO2'] = 'Cambio de fecha y horario de instalacion';
		
		$lBodyUda = str_ireplace('@_usuario_@', 	@$dataSol['N_CONTACTO']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_empresa_@', 	$dataSol['N_CLIENTE'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBodyUda);
		$lBodyUda = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO']  , $lBodyUda);
		$lBodyUda = str_ireplace('@_horario2_@', 	$dataSol['N_HORARIO2']  , $lBodyUda);
		$lBodyUda = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'], $lBodyUda);
		$lBodyUda = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_mensaje_@',     $sMensaje ,                $lBodyUda);
		$lBodyUda = str_ireplace('@_coment_uda_@',  $dataSol['REVISION'] ,     $lBodyUda);		
		$lBodyUda = str_ireplace('@_sskey_@',       $dataSol['CLAVE_SOLICITUD'],$lBodyUda);		

		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$aDataAdmin = $config->getOption('admin');					
		
		$aMailer    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => $dataSol['N_CONTACTO'],
			'inputEmails' 		 => $dataSol['EMAIL'],
			'inputTittle' 		 => 'Cambio en la solicitud',
			'inputBody' 		 => $lBodyUda,
			'inputLiveNotif'	 => 0,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
													
		$cMailing->insertRow($aMailer);	

		if($dataSol['ID_TIPO']==3){
			$aDataRev = $config->getOption('mrevision');
			$aMailerR    = Array(
				'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
				'inputDestinatarios' => $aDataRev['mails'],
				'inputEmails' 		 => $aDataRev['mails'],
				'inputTittle' 		 => 'Solicitud de Servicio',
				'inputBody' 		 => $lBodyUda,
				'inputLiveNotif'	 => 0,
				'inputFromName' 	 => 'contacto@grupouda.com.mx',
				'inputFromEmail' 	 => 'Siames - Grupo UDA'
			);
													
			$cMailing->insertRow($aMailerR);	
		}			
	}	
	
	public function acceptAdminSolicitud($dataSol, $dataUser){
		$cMailing   = new My_Model_Mailing();
		ob_start();
		include($this->realPath.'/layouts/mail/tSolicitd_aceptada_ext.html');
		$lBody = ob_get_clean();
							   
		$lBody = str_ireplace('@_usuario_@', 	$dataSol['N_CONTACTO']  , $lBody);
		$lBody = str_ireplace('@_empresa_@', 	$dataSol['N_CLIENTE'] , $lBody);
		$lBody = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBody);
		$lBody = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBody);
		$lBody = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO'] , $lBody);
		$lBody = str_ireplace('@_horario2_@', 	$dataSol['N_HORARIO2']  , $lBody);		
		$lBody = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'] , $lBody);
		$lBody = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBody);
		$lBody = str_ireplace('@_coment_uda_@',  $dataSol['REVISION'] ,     $lBody);
		
		$aMailerUda    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => $dataSol['N_CONTACTO'],
			'inputEmails' 		 => $dataSol['EMAIL'],
			'inputTittle' 		 => 'Solicitud de Servicio',
			'inputBody' 		 => $lBody,
			'inputLiveNotif'	 => 1,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
							
		$cMailing->insertRow($aMailerUda);	
	}
	

	public function acceptAdminSolicitudArrenda($dataSol, $dataUser){
		$cMailing   = new My_Model_Mailing();
		ob_start();
		include($this->realPath.'/layouts/mail/tSolicitd_aceptada.html');
		$lBody = ob_get_clean();
		
		$sDireccion = $dataSol['CALLE'].", ".$dataSol['COLONIA'].", ".$dataSol['MUNICIPIO'].", ".$dataSol['ESTADO'].", CP: ".$dataSol['CP'];
							   
		$lBody = str_ireplace('@_usuario_@', 	@$dataSol['N_CONTACTO']   , $lBody);
		$lBody = str_ireplace('@_empresa_@', 	$dataSol['N_CLIENTE'] , $lBody);
		$lBody = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBody);
		$lBody = str_ireplace('@_tipequipo_@', 	$dataSol['N_EQUIPO']   , $lBody);
		$lBody = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBody);
		$lBody = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO'] , $lBody);
		$lBody = str_ireplace('@_lugarservicio_@',$sDireccion , $lBody);
		$lBody = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'] , $lBody);
		$lBody = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBody);
		$lBody = str_ireplace('@_coment_uda_@',  $dataSol['REVISION'] ,     $lBody);	
						
		$aMailerUda    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => @$dataSol['N_CONTACTO'],
			'inputEmails' 		 => @$dataSol['EMAIL'],
			'inputTittle' 		 => 'Solicitud de Servicio',
			'inputBody' 		 => $lBody,
			'inputLiveNotif'	 => 1,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
							
		$cMailing->insertRow($aMailerUda);			
	}	
	
	public function changeSolicitudArrenda($dataSol, $dataUser){
		$cMailing   = new My_Model_Mailing();		
		ob_start();
		include($this->realPath.'/layouts/mail/tSolicitud_cambio.html');
		$lBodyUda = ob_get_clean();	
		
		$sMensaje = 'Cambio de fecha y horario de instalacion';
		$sDireccion = $dataSol['CALLE'].", ".$dataSol['COLONIA'].", ".$dataSol['MUNICIPIO'].", ".$dataSol['ESTADO'].", CP: ".$dataSol['CP'];
		
		$lBodyUda = str_ireplace('@_usuario_@', 	@$dataSol['N_CONTACTO']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_empresa_@', 	$dataSol['N_CLIENTE'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_tservicio_@', 	$dataSol['N_TIPO']     , $lBodyUda);
		$lBodyUda = str_ireplace('@_tipequipo_@', 	$dataSol['N_EQUIPO']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_fecha_@',	 	$dataSol['FECHA_CITA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_horario_@', 	$dataSol['N_HORARIO']  , $lBodyUda);
		$lBodyUda = str_ireplace('@_lugarservicio_@',$sDireccion , $lBodyUda);
		$lBodyUda = str_ireplace('@_infounit_@', 	$dataSol['INFORMACION_UNIDAD'], $lBodyUda);
		$lBodyUda = str_ireplace('@_observaciones_@', $dataSol['COMENTARIO'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_mensaje_@',     $sMensaje ,                $lBodyUda);
		$lBodyUda = str_ireplace('@_coment_uda_@',  $dataSol['REVISION'] ,     $lBodyUda);	
		$lBodyUda = str_ireplace('@_sskey_@',       $dataSol['CLAVE_SOLICITUD'],$lBodyUda);		

		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$aDataAdmin = $config->getOption('admin');					
		
		$aMailer    = Array(
			'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
			'inputDestinatarios' => @$dataSol['N_CONTACTO'],
			'inputEmails' 		 => @$dataSol['EMAIL'],
			'inputTittle' 		 => 'Cambio en la solicitud',
			'inputBody' 		 => $lBodyUda,
			'inputLiveNotif'	 => 1,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
													
		$cMailing->insertRow($aMailer);	

		if($dataSol['ID_TIPO']==3){
			$aDataRev = $config->getOption('mrevision');
			$aMailerR    = Array(
				'inputIdSolicitud'	 => $dataSol['ID_SOLICITUD'],
				'inputDestinatarios' => $aDataRev['mails'],
				'inputEmails' 		 => $aDataRev['mails'],
				'inputTittle' 		 => 'Solicitud de Servicio',
				'inputBody' 		 => $lBodyUda,
				'inputLiveNotif'	 => 0,
				'inputFromName' 	 => 'contacto@grupouda.com.mx',
				'inputFromEmail' 	 => 'Siames - Grupo UDA'
			);
													
			$cMailing->insertRow($aMailerR);	
		}			
	}

	public function notif_autorizacion($dataSol, $dataUser){
		$cMailing   = new My_Model_Mailing();		
		ob_start();
		include($this->realPath.'/layouts/mail/tNotification_aut.html');
		$lBodyUda = ob_get_clean();	
		
		$sMensaje = 'Cita Autorizada';
		
		$lBodyUda = str_ireplace('@_status_@', 	 	@$dataSol['ESTATUS']   		, $lBodyUda);
		$lBodyUda = str_ireplace('@_folio_@', 	 	@$dataSol['FOLIO']   		, $lBodyUda);
		$lBodyUda = str_ireplace('@_fechahora_@',	@$dataSol['FECHA_CITA']." ".@$dataSol['HORA_CITA']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_pasignado_@',	@$dataSol['OPERADOR']   	, $lBodyUda);
		$lBodyUda = str_ireplace('@_contacto_@', 	@$dataSol['CONTACTO']   	, $lBodyUda);
		$lBodyUda = str_ireplace('@_telcontacto_@',	@$dataSol['TELEFONO_CONTACTO'], $lBodyUda);
		$lBodyUda = str_ireplace('@_dire_@', 		@$dataSol['DIRECCION_CITA'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_refes_@', 		@$dataSol['REF_CITA']   	, $lBodyUda);
		$lBodyUda = str_ireplace('@_nombrecliente_@',@$dataSol['NOMBRE_CLIENTE'], $lBodyUda);
		$lBodyUda = str_ireplace('@_razon_@', 		@$dataSol['RAZON_SOCIAL']   , $lBodyUda);
		$lBodyUda = str_ireplace('@_tel_@', 		@$dataSol['TELEFONO_FIJO']  , $lBodyUda);
		$lBodyUda = str_ireplace('@_movil_@', 		@$dataSol['TELEFONO_MOVIL'] , $lBodyUda);
		$lBodyUda = str_ireplace('@_mail_@', 		@$dataSol['EMAIL']   		, $lBodyUda);
		$lBodyUda = str_ireplace('@_autorizo_@', 	@$dataSol['N_AUTORIZO']   	, $lBodyUda);
				
		$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$aDataAdmin = $config->getOption('notifs');					
		
		$aMailer    = Array(
			'inputIdSolicitud'	 => -1,
			'inputDestinatarios' => $aDataAdmin['mails'],
			'inputEmails' 		 => $aDataAdmin['mails'],
			'inputTittle' 		 => $sMensaje,
			'inputBody' 		 => $lBodyUda,
			'inputLiveNotif'	 => 0,
			'inputFromName' 	 => 'contacto@grupouda.com.mx',
			'inputFromEmail' 	 => 'Siames - Grupo UDA'
		);
													
		$cMailing->insertRow($aMailer);		
	}
}