<?php

            //Adapter List

interface ListViewAdapter{
    public function getLine(int $n):string;
    public function getLineCount():int;
}

class ListView{
    private $adapter;

    public function setAdapter(ListViewAdapter $adapter): void
    {
        $this->adapter = $adapter;
    }

    public function render():string{
        $s = "<ul>\n";
        for ($i=0;$i<$this->adapter->getLineCount();$i++){
              $s.="<li>".$this->adapter->getLine($i)."</li>\n";
        }
        $s.="</ul>\n";
        return $s;
    }
}

class User{
    private $name;
    private $surname;

    public function __construct($name, $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname): void
    {
        $this->surname = $surname;
    }
}

class UserRepo{
    private $users=[];

    public function __construct()
    {
        $this->users[] = new User("Vasia","Ivanov");
        $this->users[] = new User("Masha","Ivanova");
        $this->users[] = new User("Ivan","Petrov");
    }

    public function getUsers(): array
    {
        return $this->users;
    }
}

class UserRepoListViewAdapter implements ListViewAdapter{

    private $userrepo;

    public function __construct(UserRepo $userrepo)
    {
        $this->userrepo = $userrepo;
    }


    public function getLine(int $n): string
    {
        return $this->userrepo->getUsers()[$n]->getName();
    }

    public function getLineCount(): int
    {
        return count($this->userrepo->getUsers());
    }
}

/*$lva = new UserRepoListViewAdapter(new UserRepo());
$listView = new ListView();
$listView->setAdapter($lva);
echo $listView->render();*/

        ////Adapter Table HW

interface TableViewAdapter{
    public function getRow(int $i);
    public function getCell($i,$j);
    public function getRowCount();
}

class ViewTable{
    private $adapter;

    public function __construct(TableViewAdapter $adapter)
    {
        $this->adapter = $adapter;
    }
    public function render(){
        $s = "<table>";
        for ($i=0;$i<$this->adapter->getRowCount();$i++){
            $s.="<tr>";
            for ($j=0;$j<=count(array($this->adapter->getRow($i)));$j++){
                $s.="<td>".$this->adapter->getCell($i,$j)."</td>";
            }
            $s.="</tr>";
        }
        $s.="</table>";
        return $s;
    }
}

class UserRepoTableViewAdapter implements TableViewAdapter{
    private $userrepo;

    public function __construct(UserRepo $userrepo)
    {
        $this->userrepo = $userrepo;
    }

    public function getRow(int $i)
    {
        return $this->userrepo->getUsers()[$i];
    }

    public function getCell($i,$j)
    {
        $user = $this->userrepo->getUsers()[$i];
        if ($j===0)return $user->getName();
        else return $user->getSurname();
    }

    public function getRowCount()
    {
        return count($this->userrepo->getUsers());
    }
}

/*$lva = new UserRepoTableViewAdapter(new UserRepo());
$listView = new ViewTable($lva);
echo $listView->render();*/

        //Data Mapper

class UserEntity{
    private $id;
    private $name;
    private $time;

    public function __construct(int $id,string $name,int $time)
    {
        $this->id = $id;
        $this->name = $name;
        $this->time = $time;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTime(): int
    {
        return $this->time;
    }


}

class UserDTO{
    private $id;
    private $name;
    private $time;

    public function __construct(int $id,string $name,string $time)
    {
        $this->id = $id;
        $this->name = $name;
        $this->time = $time;
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getTime(): string
    {
        return $this->time;
    }
}

class EntityToDTO{
    public function toDTO(UserEntity $entity):UserDTO
    {
        $dto = new UserDTO($entity->getId(),
            $entity->getName(),
            strftime("%c",$entity->getTime()));
        return $dto;
    }
}

/*$u = new UserEntity(1,"Vasia",123456789);
$dto = (new EntityToDTO())->toDTO($u);
echo $dto->getTime();*/

        //Proxy

interface Message{
    public function ShowMess();
}

class UserMessage implements Message{

    private $mess;

    public function __construct(string $mess)
    {
        $this->mess = $mess;
    }

    public function ShowMess()
    {
        echo $this->mess;
    }
}

class ProxyUserMessage extends UserMessage{
    public function ShowMess()
    {
        echo "-------------\n";
        parent::ShowMess(); // TODO: Change the autogenerated stub
    }
}

/*Вот так можно использовать, подменили класс, хотя испоьзуем его интерфейс и
все его методы нам доступны */
/*function x(Message $x){
    $x->ShowMess();
}
$m = new ProxyUserMessage("Hello");
x($m);*/

        //Decorator

interface Booking{
    public function getPrice();
    public function getDesc();
}

class DoubleBooking implements Booking{

    public function getPrice()
    {
        return 700;
    }

    public function getDesc()
    {
        echo "Double bed\n";
    }
}

//Наследуется от класса
abstract class BookingDecorator extends DoubleBooking{
    private $booking;

    //Обязательно в кач-ве аргумента передается экземпляр класса
    public function __construct(DoubleBooking $booking)
    {
        $this->booking = $booking;
    }

    public function getPrice()
    {
        return $this->booking->getPrice();
    }
    public function getDesc()
    {
        $this->booking->getDesc();
    }
}

class ExtraWifi extends BookingDecorator{
    public function getPrice()
    {
        return parent::getPrice()+100;
    }
    public function getDesc()
    {
        parent::getDesc();
        echo "+Extra Wifi\n";
    }
}

class ExtraBed extends BookingDecorator{
    public function getPrice()
    {
        return parent::getPrice()+300;
    }
    public function getDesc()
    {
        parent::getDesc();
        echo "+Extra Bed\n";
    }
}

/*$b = new DoubleBooking();
$b = new ExtraWifi($b);
$b = new ExtraBed($b);
$b->getDesc();
echo $b->getPrice();*/

