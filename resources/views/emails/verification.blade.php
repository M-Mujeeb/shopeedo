
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="" style="box-sizing: border-box">
    @php
    $logo = get_setting('header_logo');
    $apple = get_setting('andriod-logo-app');
    $andriod = get_setting('apple-logo-app');
    @endphp
    <tr style="box-sizing: border-box">
        <td align="" valign="top" class="container" style="">
            <!-- Container -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="box-sizing: border-box">
                <tr>
                    <td align="">
                        <table width="650" border="0" cellspacing="0" cellpadding="0" class="mobile-shell">
                            <tr>
                                <td class="td" bgcolor="" style="width:650px; min-width:650px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
                                    <!-- Header -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
                                        <tr>
                                            <td class="p30-15-0" style="padding: 40px 30px 0px 30px;">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <th class="column" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td class="img m-center" style="font-size:0pt; line-height:0pt; text-align:left;"><img src="{{ uploaded_asset($logo) }}" width="" height="24" border="0" alt="" /></td>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                        <th class="column-empty" width="1" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;"></th>
                                                        <th class="column" width="120" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td class="text-header right" style="color:#000000; font-family:'Fira Mono', Arial,sans-serif; font-size:12px; line-height:16px; text-align:right;"><a href="{{ env('APP_URL') }}" target="_blank" class="link" style="color:#000001; text-decoration:none;"><span class="link" style="color:#000001; text-decoration:none;"></span></a></td>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </table>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="separator" style="padding-top: 40px; border-bottom:1px solid #D8D8D8; font-size:0pt; line-height:0pt;">&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- END Header -->

                                    <!-- Intro -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="width:1165px">
                                        <tr>
                                            <td class="p30-15" style="padding: 17px 30px 70px 30px;">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="h3 center pb10" style="color:#000000; font-family:'Ubuntu', Arial,sans-serif; font-size:35px; line-height:60px; padding-bottom:10px;">{{ $array['subject'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="h5 center blue pb30" style="font-family:'Ubuntu', Arial,sans-serif; font-size:20px; line-height:26px; text-align:left; padding-bottom:30px;"> {!! $array['content'] !!} </td>
                                                    </tr>
                                                    @if(!empty( $array['link']))
                                                    <tr>
                                                        <td class="h5 center blue pb30" style="font-family:'Ubuntu', Arial,sans-serif; font-size:20px; line-height:26px; text-align:center; color:#2e57ae; padding-bottom:30px;">
                                                            <a href="{{ $array['link'] }}" style="background: #007bff;padding: 0.9rem 2rem;font-size: 0.875rem;color:#fff;border-radius: .2rem;" target="_blank">{{ translate("Click Here") }}</a>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- END Intro -->
                                </td>
                            </tr>
                            <hr/>
                            <tr>
                                <td class="text-footer" style="padding-top: 20px; color:#1f2125; font-family:'Fira Mono', Arial,sans-serif; font-size:12px; line-height:22px; text-align:left;">
                                Please contact us if you have more queries . <strong style="color: #7D9A40">Call us at +92 423 5962508. ( Mon to Sat : 9:00 AM to 6:00 PM)</strong> 
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:center">
                                    <p style="text-align-center !important">
                                        Download shopeedo mobile app
                                        </p>
                                        <div style="display:flex;justify-content: center;gap: 5px;">
                                            <img src="{{ uploaded_asset($andriod) }}"  width="" height="24" border="0" alt="Android Play Store Logo" />  
                                            <img src="{{ uploaded_asset($apple) }}"  width="" height="24" border="0" alt="Android Play Store Logo" />
                                        </div>
                                </td>
                            </tr>
                            <td class="text-footer" style="padding-top: 20px; color:#1f2125; font-family:'Fira Mono', Arial,sans-serif; font-size:12px; line-height:22px; text-align:left;">
                                You have received this email because you or someone else has confirmed the email address. This would like to receive email communication from Shopeedo. We will never share your personal information (such as your email address with any other 3rd party without your consent).

This email was sent by: F1, First floor, Ghani plaza, 196-A, Main Multan Road, Lahore, Pakistan.
                                </td>
                           

                        </table>
                    </td>
                </tr>
            </table>
            <!-- END Container -->
        </td>
    </tr>
</table>


