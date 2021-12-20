<?php

class Mentoring{

     private $db;
    private $dbh;

    public function __construct(){
        $this->dbh = new Connection;
        $this->db = $this->dbh->getConn();
    }

    public function listPertemuan(){
        $listMateri = $this->db->query('SELECT * FROM pertemuan');
        return $listMateri;
    }

    public function detailPertemuan($id){
        $statement = $this->db->prepare('SELECT pertemuan_ke, materi FROM pertemuan WHERE id = ?');
        $statement->bind_param('i', $id);
        $statement->execute();

        return $statement->get_result()->fetch_assoc();
    }

    public function tambahPertemuan($data){
        $jadwal = htmlspecialchars(trim(date('Y-m-d', strtotime($data['jadwal']))));
        $pertemuan_ke = htmlspecialchars(trim($data['pertemuan_ke']));
        $materi = htmlspecialchars(trim($data['materi']));

        try{
            $this->db->begin_transaction();

            $statement = $this->db->prepare('SELECT kelompok FROM users WHERE nim = ?');
            $statement->bind_param('i', $_SESSION['user']['nim']);
            $statement->execute();
            $kelompok = $statement->get_result()->fetch_assoc()['kelompok'];

            $statement =$this->db->prepare("INSERT INTO pertemuan VALUES ('', ?, ?, ?, ?)");
            $statement->bind_param("isss", $kelompok, $jadwal, $pertemuan_ke, $materi);
            $statement->execute();
        
            if($this->db->commit() == true){
                $_SESSION['berhasil'] = "Selamat, berhasil menambahkan materi";
            }
            else{
                $_SESSION['gagal'] = "Maaf, tidak berhasil menambahkan materi";
            }
        }
        catch(Exception $e){
            $this->db->rollback();
            $_SESSION['gagal'] = "Proses pendafataran gagal";
        } 

        $statement->close();
        header("Location: ".BASEURL."/views/mentoring");  
    }

    public function deletePertemuan($id){
        $statement = $this->db->prepare('DELETE FROM pertemuan WHERE id = ?');
        $statement->bind_param('i', $id);
        $statement->execute();
        
        if($this->db->affected_rows > 0){
            $_SESSION['berhasil'] = 'Data berhasil dihapus';
        }
        else{
            $_SESSION['gagal'] = 'Data gagal dihapus';
        }

        $statement->close();
        header('Location: '.BASEURL.'/views/mentoring');
    }



}