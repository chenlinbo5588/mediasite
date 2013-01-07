<?php

class SendMail {
/**
	 +----------------------------------------------------------
 * 发送邮件函数
	 +----------------------------------------------------------
 * @param string $subject 邮件标题
 * @param string $mailbody 邮件内容
 * @param string $addr 邮件地址
 * @param string $name 收件人名称
 * @param array $attachment array(
									'filename1' => 'filecontent1',
									'filename2' => 'filecontent2',
									....
								) 邮件附件
	 +----------------------------------------------------------
 * @return array(status=>true|false,msg=errorinfo)
	 +----------------------------------------------------------
 */


	public function sendmail($subject,$mailbody,$addr,$name='',$attachment=array(),$bcc = '') {
		header('Content-type:text/html;charset=utf-8');
		vendor("PHPMailer.class#phpmailer");						//从PHPMailer目录导入class.phpmailer.php类文件
		$mail = new PHPMailer(true);								// the true param means it will throw exceptions on errors, which we need to catch

		//$mail->IsSMTP();											// telling the class to use SMTP
	 
		$config = is_array($config) ? $config : C('SYSTEM_EMAIL');

		//dump($list);

		try {
			//$mail->SMTPDebug	= false;							// 改为2可以开启调试
			//$mail->SMTPAuth		= true;								// enable SMTP authentication
			$mail->Host			= $SmtpHost;						// sets the SMTP server
			$mail->Port			= $SmtpPort;						// set the SMTP port for the GMAIL server
			$mail->CharSet		= "UTF-8";							// 这里指定字符集！解决中文乱码问题
			$mail->Encoding		= "base64";
			$mail->Username		= $SmtpAuthName;					// SMTP account username
			$mail->Password		= $SmtpAuthPasswd;					// SMTP account password
            if(is_array($addr)) {
                foreach($addr as $address) {
                    if(is_array($address)) {
                        $mail->AddAddress($address[0], $address[1]);
                    } else {
                        $mail->AddAddress($address, $name);
                    }
                }
            } else {
                $mail->AddAddress($addr, $name);
            }
            if($bcc != '') {
                if(is_array($bcc)) {
                    foreach($bcc as $address) {
                        $mail->AddBCC($address);
                    }
                } else {
                    $mail->AddBCC($bcc);
                }
            }
			$mail->SetFrom($SmtpSender, $SmtpSenderName);			//发送者邮箱
			$mail->AddReplyTo($SmtpReplyto, $SmtpReplytoName);		//回复到这个邮箱
			$mail->Subject		= $subject;
			$body				= $mailbody;
			
			$mail->MsgHTML($body);
			if(!empty($attachment))
			{
				$attCount = count($attachment);
				for($i=0;$i<$attCount;$i++)
				{
					$att = $attachment[$i];
					$mail->AddAttachment($att['string'],$att['filename']);
				}
			}
			$mail->Send();
			return array('status'=>true,'msg'=>'Message Sent OK');
		}
		catch (phpmailerException $e) {
			return array('status'=>false,'error'=>$e->errorMessage());
		}
		catch (Exception $e) {
			return array('status'=>false,'error'=>$e->getMessage());
		}
	}
}