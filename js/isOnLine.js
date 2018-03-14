/**
 * O objetivo desse código é verificar constantemente se existe conexão com a internet e caso não exista
 * então bloqueia a página do usuário.
 * 
 * @code
 * 
 * @endcode
 * 
 * @file                isOnLine.js
 * @license		http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 * @link		
 * @copyright           2016 F71
 * @author		Jacques <jacques@f71.com.br>
 * @package             isOnLine
 * @access              public  
 * 
 * @version: 0.0.0000 - 28/10/2016 - Jacques - Versão Inicial 
 * @version: 0.0.0000 - 11/11/2016 - Jacques - Acrescentado as variáveis ping_tot e ping_time para controle variável do ping e alteração do número de ping on fail
 * 
 * @todo 
 * 
 */

/**
 * Esta função e executada em um intervalo de tempo determinado por setInterval para verificar estado de conexão
 * 
 * @param  {string} url
 * @return {float} 
 */

var offLine = 0;
var ping_tot = 6;
var ping_time = 5000;
var lock = 0;

setInterval(onLine, ping_time);


function hasInternet() {
    var s = $.ajax({
        type: "HEAD",
        url: window.location.href.split("?")[0] + "?" + Math.random(),
        async: false
    }).status;

    return s >= 200 && s < 300 || s === 304;
};

function onLine() {

    if (hasInternet()) {

        if(lock) {
            
            $.unblockUI();
            lock = offLine = 0;
            
        }

    } else {

        if (offLine == ping_tot) {
            lock = 1;
            $.blockUI({
                message: "off-line",
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }
            });
        }    

        offLine++;

    }

}



