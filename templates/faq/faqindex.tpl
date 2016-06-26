 <table class="collapse3" style="BORDER-COLLAPSE: collapse" width="100%" border="0">
                          <tr>
                                  
                      <td width="100%" class="news_back"> <font face="Verdana" size="1"><span lang="en-us" style="letter-spacing: 1px;">  
                        </span>
                        <span style="letter-spacing: 1px">{$section}</span><span style="letter-spacing: 1px;">
                        </span></font></td>
                                </tr>
                                
                    <tr> 
                      <td width="100%" bgColor="#000000" valign="top" height="300">
					  {php}
$section=strtolower(str_replace(" ","_",$section));
if (file_exists($gameroot."/faq/".$section.".html")){
	include ($section.".html");
	}
{/php}
 
 
  <p>&nbsp;</p>
                      </td>
                                </tr>              
                              </table>