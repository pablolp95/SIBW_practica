<?php
include_once '../app/controllers/ReservesController.php';
include_once '../app/models/Reserve.php';
include_once '../app/models/Room.php';
include_once '../app/models/Rooms.php';
include_once '../app/models/Roomtypes.php';


class ReserveView
{
    function print_reserve($step){

        $this->print_progress($step);
        switch ($step){
            case 'select_room':
                $this->print_select_room();
                break;
            case 'introduce_info':
                $this->print_introduce_info();
                break;
            case 'confirm':
                $this->print_confirm();
                break;
            case 'summary':
                $this->print_summary();
                break;
        }
    }

    private function print_select_room(){
        echo '<section>
                <div class="container">
                    <div class="row select-room-title">
                        <div class="col-sm-12 nopadding">
                            <h1>¿Cuando quieres alojarte?</h1>
                        </div>
                    </div>
                    <form role="form" name="myForm" method="POST" action="?page=reserve&step=introduce_info">
                            <div class="row section">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="stating_date">Entrada</label>
                                        <input type="date" class="form-control input-lg input-style" id="starting_date" name="starting_date" required>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="ending_date">Salida</label>
                                        <input type="date" class="form-control input-lg input-style" id="ending_date" name="ending_date" required>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Adultos</label>
                                        <select class="form-control input-lg input-style" id="adults_number" value ="0" name="adults_number" required>';
                                            for($i=0;$i<11;++$i){
                                                echo '<option value="'.$i.'"';
                                                if($_SESSION['adults_number'] == $i){
                                                    echo'selected="selected"';
                                                }
                                                echo'>'.$i.'</option>';
                                            }
                                    echo'
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Niños</label>
                                        <select class="form-control input-lg input-style" id="children_number" name="children_number" required>';
                                            for($i=0;$i<11;++$i){
                                                echo '<option value="'.$i.'"';
                                                if($_SESSION['children_number'] == $i){
                                                    echo'selected="selected"';
                                                }
                                                echo'>'.$i.'</option>';
                                            }
                                    echo'
                                        </select>
                                    </div>
                                </div>';
                                echo '
                                <div class="col-sm-2 select-room-submit">
                                    <button class="button btn-lg" type="button" style="text-align: center" onclick="loadDoc(document.getElementById(\'starting_date\').value ,document.getElementById(\'ending_date\').value,document.getElementById(\'adults_number\').value)">Comprobar</button class="button btn-lg">
                                </div>

                            </div>
                            <div class="row table-room" id="roomstype">

                            </div>
                    </form>';
        echo'
                </div>
            </section>';
    }

    private function print_introduce_info(){
        session_start();
        $_SESSION['starting_date'] = $_POST['starting_date'];
        $_SESSION['ending_date'] = $_POST['ending_date'];
        $_SESSION['adults_number'] = $_POST['adults_number'];
        $_SESSION['children_number'] = $_POST['children_number'];
        $_SESSION['select_Individual'] = $_POST['select_Individual'];
        $_SESSION['select_Doble'] = $_POST['select_Doble'];
        $_SESSION['select_Triple'] = $_POST['select_Triple'];
        $_SESSION['select_Familiar'] = $_POST['select_Familiar'];
        $_SESSION['promotion_code'] = $_POST['promotion_code'];
        $_SESSION['total_amount_submit'] = $_POST['total_amount_submit'];

        echo '
            <section>
                <div class="container">
                    <div class="row select-room-title">
                        <div class="col-sm-12 nopadding">
                            <h1>Por favor, introduce tus datos personales</h1>
                        </div>
                    </div>
                    <form role="form" name="myForm" method="POST" action="?page=reserve&step=confirm">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name">Nombre:</label>
                                    <input type="text" class="form-control input-lg input-style" id="name" name="name" value="'.$_SESSION['name'].'" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="surname">Apellidos:</label>
                                    <input type="text" class="form-control input-lg input-style" id="surname" name="surname" value="'.$_SESSION['surname'].'" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="city">Ciudad:</label>
                                    <input type="text" class="form-control input-lg input-style" id="city" name="city" value="'.$_SESSION['city'].'" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="address">Dirección:</label>
                                    <input type="text" class="form-control input-lg input-style" id="address" name="address" value="'.$_SESSION['address'].'" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name">Teléfono</label>
                                    <input type="text" class="form-control input-lg input-style" id="phone" name="phone" value="'.$_SESSION['phone'].'" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="surname">E-mail:</label>
                                    <input type="text" class="form-control input-lg input-style" id="email" name="email" value="'.$_SESSION['email'].'" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="surname">Observaciones (Opcional):</label>
                                    <textarea class="form-control input-lg noresize input-style" rows="5" id="observations" name="observations">'.$_SESSION['observations'].'</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row next">
                            <div class="col-sm-offset-10 col-sm-2">
                                <button class="button btn-lg" style="text-align: center">Siguiente</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>';
    }

    private function print_confirm(){
        session_start();
        $_SESSION['name'] = $_POST['name'];
        $_SESSION['surname'] = $_POST['surname'];
        $_SESSION['city'] = $_POST['city'];
        $_SESSION['address'] = $_POST['address'];
        $_SESSION['phone'] = $_POST['phone'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['observations'] = $_POST['observations'];

        echo '
            <section>
                <div class="container">
                    <div class="row select-room-title">
                        <div class="col-sm-12 nopadding">
                            <h1>Confirmación de reserva</h1>
                        </div>
                    </div>
                    <form role="form" name="myForm" method="POST" action="?page=reserve&action=store">
                        <input name="starting_date" type="hidden" value="'.$_SESSION['starting_date'].'">
                        <input name="ending_date" type="hidden" value="'.$_SESSION['ending_date'].'">
                        <input name="adults_number" type="hidden" value="'.$_SESSION['adults_number'].'">
                        <input name="children_number" type="hidden" value="'.$_SESSION['children_number'].'">
                        <input name="select_Individual" type="hidden" value="'.$_SESSION['select_Individual'].'">
                        <input name="select_Doble" type="hidden" value="'.$_SESSION['select_Doble'].'">
                        <input name="select_Triple" type="hidden" value="'.$_SESSION['select_Triple'].'">
                        <input name="select_Familiar" type="hidden" value="'.$_SESSION['select_Familiar'].'">
                        <input name="promotion_code" type="hidden" value="'.$_SESSION['promotion_code'].'">
                        <input name="name" type="hidden" value="'.$_SESSION['name'].'">
                        <input name="surname" type="hidden" value="'.$_SESSION['surname'].'">
                        <input name="city" type="hidden" value="'.$_SESSION['city'].'">
                        <input name="address" type="hidden" value="'.$_SESSION['address'].'">
                        <input name="phone" type="hidden" value="'.$_SESSION['phone'].'">
                        <input name="email" type="hidden" value="'.$_SESSION['email'].'">
                        <input name="observations" type="hidden" value="'.$_SESSION['observations'].'">
                        <input name="total_amount_submit" type="hidden" value="'.$_SESSION['total_amount_submit'].'">
                        <div class="row">
                            <div class="col-sm-12 warranty">
                                <h4>Garantía de reserva</h4>
                            </div>
                               
                            <div class="col-sm-6">
                                 <div class="form-group">
                                    <label for="cardholder">Titular de la tarjeta:</label>
                                    <input type="text" class="form-control input-lg input-style" id="cardholder" name="cardholder" required>
                                 </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="cardnumber">Número de tarjeta:</label>
                                    <input type="text" class="form-control input-lg input-style" id="card_number" name="card_number" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="card_expiration_month">Mes de caducidad de la tarjeta:</label>
                                    <select class="form-control input-lg input-style" type="text" id="card_expiration_month" name="card_expiration_month" >
                                        <option value="01">01</option>
                                        <option value="02">02</option>
                                        <option value="03">03</option>
                                        <option value="04">04</option>
                                        <option value="05">05</option>
                                        <option value="06">06</option>
                                        <option value="07">07</option>
                                        <option value="08">08</option>
                                        <option value="09">09</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="card_expiration_year">Año de caducidad de la tarjeta:</label>
                                    <select class="form-control input-lg input-style" type="text" id="card_expiration_year" name="card_expiration_year" >
                                        <option value="16">16</option>
                                        <option value="17">17</option>
                                        <option value="18">18</option>
                                        <option value="19">19</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="card_type">Tipo de tajeta:</label>
                                    <select class="form-control input-lg input-style" type="text" id="card_type" name="card_type" >
                                        <option value="VISA">VISA</option>
                                        <option value="MasterCard">MasterCard</option>
                                        <option value="AmericanExpress">American Express</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="card_cvc">CVC:</label>
                                    <input class="form-control input-lg noresize input-style" id="card_cvc" name="card_cvc" required>
                                </div>
                            </div>
                            </div>
                            <div class="row next">
                                <div class="col-sm-offset-10 col-sm-2">
                                    <button class="button btn-lg" style="text-align: center">Siguiente</button>
                                </div>
                            </div>
                    </form>
                </div>
            </section>';
    }

    private function print_summary(){
        session_start();

        echo '
            <section>
                <div class="container">
                    <div class="row select-room-title">
                        <div class="col-sm-12 nopadding">
                            <h1>Resumen</h1>
                        </div>
                    </div>
                    <div class="row" id="final_summary">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p><strong>Fecha de entrada:</strong> '.$_SESSION['starting_date'].'</p>
                                    <p><strong>Fecha de salida:</strong> '.$_SESSION['ending_date'].'</p>
                                    <p><strong>Número de adultos:</strong> '.$_SESSION['adults_number'].'</p>
                                    <p><strong>Número de niños:</strong> '.$_SESSION['children_number'].'</p>
                                </div>
                                <div class="col-sm-6">
                                    <p><strong>Nombre:</strong> '.$_SESSION['name'].'</p>
                                    <p><strong>Apellidos:</strong> '.$_SESSION['surname'].'</p>
                                    <p><strong>Ciudad:</strong> '.$_SESSION['city'].'</p>
                                    <p><strong>Dirección:</strong> '.$_SESSION['address'].'</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <p><strong>Habitaciones seleccionadas:</strong></p>
                                    <div id="selected">';
                                    if($_SESSION['select_Individual'] > 0){
                                        echo'<p style="font-size: 1em;margin-left: 20px">-Individual: '.$_SESSION['select_Individual'];
                                    }
                                    if($_SESSION['select_Doble'] > 0){
                                        echo'<p style="font-size: 1em;margin-left: 20px">-Doble: '.$_SESSION['select_Doble'];
                                    }
                                    if($_SESSION['select_Triple'] > 0){
                                        echo'<p style="font-size: 1em;margin-left: 20px">-Triple: '.$_SESSION['select_Triple'];
                                    }
                                    if($_SESSION['select_Familiar'] > 0){
                                        echo'<p style="font-size: 1em;margin-left: 20px">-Familiar: '.$_SESSION['select_Familiar'];
                                    }
                                    echo '               
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <p><strong>TOTAL: '.$_SESSION['total_amount_submit'].'<span class="glyphicon glyphicon-euro"/></strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>';
        session_destroy();
    }

    private function print_progress($step){
        switch ($step){
            case 'select_room':
                $one = 'active';
                $second = 'disabled';
                $third = 'disabled';
                $fourth = 'disabled';
                break;
            case 'introduce_info':
                $one = 'complete';
                $second = 'active';
                $third = 'disabled';
                $fourth = 'disabled';
                break;
            case 'confirm':
                $one = 'complete';
                $second = 'complete';
                $third = 'active';
                $fourth = 'disabled';
                break;
            case 'summary':
                $one = 'complete';
                $second = 'complete';
                $third = 'complete';
                $fourth = 'complete';
                break;
        }

        echo '
        <div class="container-fluid steps">
            <div class="row bs-wizard" style="border-bottom:0;">
                <div class="col-xs-3 bs-wizard-step '.$one.'">
                  <div class="text-center bs-wizard-stepnum">Selecciona tu habitación</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                </div>
                
                <div class="col-xs-3 bs-wizard-step '.$second.'">
                  <div class="text-center bs-wizard-stepnum">Introduce tus datos</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                </div>
                
                <div class="col-xs-3 bs-wizard-step '.$third.'">
                  <div class="text-center bs-wizard-stepnum">Confirmación</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                </div>
                
                <div class="col-xs-3 bs-wizard-step '.$fourth.'">
                  <div class="text-center bs-wizard-stepnum">Resguardo</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                </div>
            </div>
        </div>
        </div>
        ';
    }
}