<?php
    function showEnable($key) {
        $key = strval($key);
        $show = '';
        switch($key) {
            case '0':
                $show = 'Paused';
                break;
            case '1':
                $show = 'Valid';
                break;
            case '2':
                $show = 'Deleted';
                break;
            default:
                $show = 'Valid';
                break;
        }
        return $show;
    }


    function sendmail($subject,$mailbody,$sendto,$sender,$attachment=array(),$bcc = '') {
        
        //header('Content-type:text/html;charset=utf-8');
        vendor("PHPMailer.class#phpmailer");                        //从PHPMailer目录导入class.phpmailer.php类文件
        $mail = new PHPMailer(true);                                // the true param means it will throw exceptions on errors, which we need to catch
        $mail->IsSMTP();                                          // telling the class to use SMTP
     
        $config = is_array($config) ? $config : C('SYSTEM_EMAIL');

        try {
            //$mail->SMTPDebug  = false;                            // 改为2可以开启调试
            $mail->SMTPAuth       = true;                             // enable SMTP authentication
            $mail->Host         = $config['SMTP_HOST'];                        // sets the SMTP server
            $mail->Port         = $config['SMTP_PORT'];                        // set the SMTP port for the GMAIL server
            $mail->CharSet      = "UTF-8";                          // 这里指定字符集！解决中文乱码问题
            $mail->Encoding     = "base64";
            $mail->Username     = $config['SMTP_USER'];                    // SMTP account username
            $mail->Password     = $config['SMTP_PASS'];                  // SMTP account password

            if(is_array($sendto)) {
                foreach($sendto as $address) {
                    if(is_array($address)) {
                        $mail->AddAddress($address[0], $address[1]);
                    } else {
                        $mail->AddAddress($address);
                    }
                }
            } else {
                $mail->AddAddress($sendto);
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
            $mail->SetFrom($config['SMTP_USER'],$config['SMTP_NAME']);
            if(is_array($sender)) {
                $mail->AddReplyTo($sender[0], $sender[1]);      //回复到这个邮箱
            } else {
                $mail->AddReplyTo($sender);
            }
            $mail->Subject      = $subject;
            $body               = $mailbody;
            
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

    /**
     * 获取页面内容
     *
     * @param string $url 页面地址
     * @param string 页面内容
     */
    function getUrlCon($url) {
        $html = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        ob_start();
        curl_exec($ch);
        $html = ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        return $html;
    }
?>