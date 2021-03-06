<?php
include_once '../app/models/Rooms.php';
include_once '../app/models/Room.php';
include_once '../resources/views/intranet/IntranetRoomsView.php';
include_once '../app/controllers/Controller.php';

class RoomsController extends Controller {
    public function index(){
        $rooms = new Rooms();
        $list = $rooms->all();
        IRoomsView::print_index($list);
    }

    public function create(){
        IRoomsView::print_create();
    }

    public function store(){
        if(isset($_POST['select'])&& isset($_POST['number_room'])){
            $room = new Room(null,$_POST['select'], $_POST['number_room']);
            $rooms = new Rooms();
            if($rooms->save($room)){
                header("Location: /?page=intranet&section=rooms");
            }
            else{
                header("Location: /?page=intranet&section=rooms&action=create");

            }
        }
    }

    public function show($id){
        $rooms = new Rooms();
        $room = $rooms->find($id);
        IRoomsView::print_room($room);

    }

    public function edit($id){
        $rooms = new Rooms();
        $room = $rooms->find($id);
        IRoomsView::print_edit($room);
    }

    public function update($id){
        $rooms = new Rooms();
        if(isset($_POST['select'])&& isset($_POST['number_room'])){
            $room = new Room($id, $_POST['select'], $_POST['number_room']);
            $room->set_reserve_associated($_POST['reserve_associated']);
            if($rooms->update($room)){
                header("Location: /?page=intranet&section=rooms");
            }
            else{
                header("Location: /?page=intranet&section=rooms&action=edit&id={$room->get_id()}");
            }
        }
    }

    public function delete($id){
        $rooms = new Rooms();
        if($rooms->delete($id))
            header("Location: /?page=intranet&section=rooms");

    }
}

?>