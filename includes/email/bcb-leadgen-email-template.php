<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        #outlook a{padding:0;}
        body{width:100% !important; background-color:#41849a;-webkit-text-size-adjust:none; -ms-text-size-adjust:none;margin:0 !important; padding:0 !important;}  
        .ReadMsgBody{width:100%;} 
        .ExternalClass{width:100%;}
        ol li {margin-bottom:15px;}
            
        img{height:auto; line-height:100%; outline:none; text-decoration:none;}
        #backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}
            
        p {margin: 1em 0;}
            
        h1, h2, h3, h4, h5, h6 {font-family:Arial, Helvetica, sans-serif; line-height: 100% !important;}
            
        table td {border-collapse:collapse;}
            
        .yshortcuts, .yshortcuts a, .yshortcuts a:link,.yshortcuts a:visited, .yshortcuts a:hover, .yshortcuts a span { color: black; text-decoration: none !important; border-bottom: none !important; background: none !important;}
            
        .im {color:black;}
        div[id="tablewrap"] {
                width:100%; 
                max-width:600px!important;
            }
        table[class="fulltable"], td[class="fulltd"] {
                max-width:100% !important;
                width:100% !important;
                height:auto !important;
            }
                    
        @media screen and (max-device-width: 430px), screen and (max-width: 430px) { 
                td[class=emailcolsplit]{
                    width:100%!important; 
                    float:left!important;
                    padding-left:0!important;
                    max-width:430px !important;
                }
            td[class=emailcolsplit] img {
            margin-bottom:20px !important;
            }
        }
    </style>
</head>
    <body style="width:100% !important; margin:0 !important; padding:0 !important; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; background-color:#FFFFFF;">
        <table cellpadding="0" cellspacing="0" border="0" id="backgroundTable" style="height:auto !important; margin:0; padding:0; width:100% !important; background-color:#FFFFFF; color:#222222;">
            <tr>
                <td>
                <div id="tablewrap" style="width:100% !important; max-width:600px !important; text-align:center !important; margin-top:0 !important; margin-right: auto !important; margin-bottom:0 !important; margin-left: auto !important;">
                    <table id="contenttable" width="600" align="center" cellpadding="0" cellspacing="0" border="0" style="background-color:#FFFFFF; text-align:center !important; margin-top:0 !important; margin-right: auto !important; margin-bottom:0 !important; margin-left: auto !important; border:none; width: 100% !important; max-width:600px !important;">
                    <tr>
                        <td width="100%">
                            <table bgcolor="#0073b1" border="0" cellspacing="0" cellpadding="0" width="100%" style="table-layout: fixed; overflow: hidden;">
                                <?php if( ! empty( $logo ) ) : ?>
                                
                                <tr><td colspan="2" bgcolor="#000000" style="color: #ffffff; text-align: left; padding: 15px;" align="left"><?php printf( '<img src="%s" style="height: 60px; width: auto; max-width: 100%%; display: block;" />', esc_url( $logo ) ); ?></td></tr>
                                
                                <?php endif; ?>
                                
                                <tr>
                                <td width="55%" align="left" style="padding: 0 15px;"><h1 style="color: #ffffff; font-family:Arial, Helvetica, sans-serif; font-size: 32px; text-align: left;"><?php print $title; ?></h1></td>
                                <td width="45%" style="text-align:center; padding: 45px 15px 0px;" valign="bottom"><?php if( ! empty( $banner ) ) : ?><a href="<?php print esc_url( $permalink ); ?>"><?php printf( '<img src="%s" style="max-width: 100%%; display: block;" />', esc_url( $banner ) ); ?></a><?php endif; ?></td>
                                </tr>
                        </table>
                        <table bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="25" width="100%">
                                <tr>
                                    <td width="100%" bgcolor="#ffffff" style="text-align:left;">
                                        <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:19px; margin-top:0; margin-bottom:20px; padding:0; font-weight:normal;"><?php print $content; ?></p>
                                    </td>
                                </tr>
                        </table>
                        <table bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                <td width="100%" bgcolor="#ffffff" style="text-align:center;"><a style="font-weight:bold; text-decoration:none;" href="<?php print esc_url( $permalink ); ?>"><div style="display:inline-block; height:auto !important;background-color:#0073b1;padding-top:15px;padding-right:25px;padding-bottom:15px;padding-left:25px;border-radius:8px;color:#ffffff;font-size:24px;font-family:Arial, Helvetica, sans-serif;">Download White Paper</div></a></td>
                                </tr>
                        </table>
                        </td>
                    </tr>
                </table>
                </div>
                </td>
            </tr>
        </table> 
    </body>
</html>