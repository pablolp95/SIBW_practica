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
        session_start();
        if(isset($_SESSION['expire'])){
            if(time()>$_SESSION['expire']){
                session_unset();
                session_destroy();
                echo '<script>alert("El plazo de tiempo máximo para realizar una reserva ha expirado")</script>';
                header("Location: /?page=reserve&step=select_room");
                exit();
            }
        }
        else{
            $_SESSION['expire'] = time()+(5*60);
        }

        $isset = false;
        if(isset($_POST['starting_date_submit'],$_POST['ending_date_submit'],$_POST['adults_number'],$_POST['children_number'])){
            $_SESSION['starting_date_submit'] = $_POST['starting_date_submit'];
            $_SESSION['ending_date_submit'] = $_POST['ending_date_submit'];
            $_SESSION['adults_number'] = $_POST['adults_number'];
            $_SESSION['children_number'] = $_POST['children_number'];
            $isset = true;
        }

        echo '
            <section>
                <div class="container">
                    <div class="row select-room-title">
                        <div class="col-sm-12 nopadding">
                            <h1>¿Cuando quieres alojarte?</h1>
                        </div>
                    </div>
                    <form role="form" name="myForm" method="POST" action="?page=reserve&step=select_room">
                            <div class="row section">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="stating_date_submit">Entrada</label>
                                        <input type="date" class="datepicker form-control input-lg input-style" id="starting_date_submit" name="starting_date_submit" value="'.$_SESSION['starting_date_submit'].'"required>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="ending_date">Salida</label>
                                        <input type="date" class="datepicker form-control input-lg input-style" id="ending_date_submit" name="ending_date_submit" value="'.$_SESSION['ending_date_submit'].'"required>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Adultos</label>
                                        <select class="form-control input-lg input-style" id="adults_number" name="adults_number" required>';
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
                                    <button class="button btn-lg" style="text-align: center">Comprobar</button>
                                </div>
                            </div>
                    </form>';
        if($isset){
            $reserves = new ReservesController();
            $availables = $reserves->getRoomsAvailable($_POST['starting_date'],$_POST['ending_date']);//Obtengo para cada tipo el número de habitaciones disponibles
            $roomtypes = new Roomtypes();//Objeto contendor de tipos de habitaciones
            $roomtype_list = array();//Esta variable almaenara los tipos de habitaciones
            //Para cada tipo de habitacion obtengo su objeto para manejar la información relacionada a ella
            while($element = current($availables)) {
                $roomtype = $roomtypes->findByName(key($availables));
                array_push($roomtype_list,$roomtype);
                next($availables);
            }
            $available = false;
            foreach ($availables as $type=>$number){
                if($number > 0){
                    $available = true;
                }
            }
            if($available){
                echo'
                    <form role="form" name="myForm" method="POST" action="?page=reserve&step=introduce_info">
                        <div class="row table-room">
                            <div class="panel panel-default">
                                <table class="table table-roomtypes"> 
                                    <thead> 
                                        <tr> 
                                            <th>Tipo</th> 
                                            <th>Precio</th> 
                                            <th>Cantidad</th> 
                                        </tr> 
                                    </thead> 
                                    <tbody>';
                                    foreach ($roomtype_list as $roomtype){
                                        if($availables[$roomtype->getName()] > 0){
                                        echo '
                                            <tr> 
                                                <td>'.$roomtype->getName().'</td> 
                                                <td id="price_'.$roomtype->getName().'">'.$roomtype->getBasePrice().'</td>
                                                <td>
                                                    <select class="form-control input-lg input-style" id="select_'.$roomtype->getName().'" name="select_'.$roomtype->getName().'">';
                                                        echo '<option value="0">0</option>';
                                                        for($i=1;$i<=$availables[$roomtype->getName()];$i++){
                                                            echo '<option value="'.$i.'">'.$i.'</option>';
                                                        }
                                                        echo'
                                                    </select>
                                                </td>
                                            </tr>';
                                        }
                                    }
                echo'
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row next">
                            <div class="col-sm-2 nopadding">
                                <p>TOTAL: <span id="total_amount">0</span>€</p>
                            </div>
                            <div class="col-sm-offset-8 col-sm-2">
                                <button class="button btn-lg" style="text-align: center">Siguiente</button>
                            </div>
                        </div>
                    </form>';
            }
            else{
                echo '
                    <div class="row">
                        <div class="col-sm-12 noroom-message text-center">
                            <h3>Lo sentimos, no tenemos habitaciones disponibles para estas fechas.</h3>
                        </div>
                    </div>';
            }
        }
        echo'
                </div>
            </section>';
    }

    private function print_introduce_info(){
        session_start();
        $_SESSION['select_Individual'] = $_POST['select_Individual'];
        $_SESSION['select_Doble'] = $_POST['select_Doble'];
        $_SESSION['select_Triple'] = $_POST['select_Triple'];
        $_SESSION['select_Familiar'] = $_POST['select_Familiar'];

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
        session_destroy();

        echo '
            <section>
                <div class="container">
                    <div class="row select-room-title">
                        <div class="col-sm-12 nopadding">
                            <h1>Confirmación de reserva</h1>
                        </div>
                    </div>
                    <form role="form" name="myForm" method="POST" action="?page=reserve&action=store">
                        <input name="starting_date_submit" type="hidden" value="'.$_SESSION['starting_date_submit'].'">
                        <input name="ending_date_submit" type="hidden" value="'.$_SESSION['ending_date_submit'].'">
                        <input name="adults_number" type="hidden" value="'.$_SESSION['adults_number'].'">
                        <input name="children_number" type="hidden" value="'.$_SESSION['children_number'].'">
                        <input name="select_Individual" type="hidden" value="'.$_SESSION['select_Individual'].'">
                        <input name="select_Doble" type="hidden" value="'.$_SESSION['select_Doble'].'">
                        <input name="select_Triple" type="hidden" value="'.$_SESSION['select_Triple'].'">
                        <input name="select_Familiar" type="hidden" value="'.$_SESSION['select_Familiar'].'">
                        <input name="name" type="hidden" value="'.$_SESSION['name'].'">
                        <input name="surname" type="hidden" value="'.$_SESSION['surname'].'">
                        <input name="city" type="hidden" value="'.$_SESSION['city'].'">
                        <input name="address" type="hidden" value="'.$_SESSION['address'].'">
                        <input name="phone" type="hidden" value="'.$_SESSION['phone'].'">
                        <input name="email" type="hidden" value="'.$_SESSION['email'].'">
                        <input name="observations" type="hidden" value="'.$_SESSION['observations'].'">
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
                                    <label for="card_type">Tipo de tajeta:</label>
                                    <input type="text" class="form-control input-lg input-style" id="card_type" name="card_type" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="card_expiration_month">Mes de caducidad de la tarjeta:</label>
                                    <input type="text" class="form-control input-lg input-style" id="card_expiration_month" name="card_expiration_month" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="card_expiration_year">Mes de caducidad de la tarjeta:</label>
                                    <input class="form-control input-lg noresize input-style" id="card_expiration_year" name="card_expiration_year" required>
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
        echo '
            <section>
                <div class="container">
                    <div class="row select-room-title">
                        <div class="col-sm-12 nopadding">
                            <h1>Resumen</h1>
                        </div>
                    </div>
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
                                    <label for="card_type">Tipo de tajeta:</label>
                                    <input type="text" class="form-control input-lg input-style" id="card_type" name="card_type" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="card_expiration_month">Mes de caducidad de la tarjeta:</label>
                                    <input type="text" class="form-control input-lg input-style" id="card_expiration_month" name="card_expiration_month" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="card_expiration_year">Mes de caducidad de la tarjeta:</label>
                                    <input class="form-control input-lg noresize input-style" id="card_expiration_year" name="card_expiration_year" required>
                                </div>
                            </div>
                                
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="card_cvc">CVC:</label>
                                    <input class="form-control input-lg noresize input-style" id="card_cvc" name="card_cvc" required>
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