<?php
include 'koneksi.php';
$data_pengunjung = query("SELECT * FROM pengunjung ORDER BY tanggal_kunjung DESC");
$Buku = query("SELECT * FROM buku");
var_dump($Buku);

class Siswa
{
    protected $nis;
    protected $nama;
    protected $email;
    public function __construct($nis, $nama ,$email)
    {
        $this->nis = $nis;
        $this->nama = $nama;
        $this->email = $email;
    }
    public function infoSiswa()
    {
        global $conn;
        $siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$this->nis'");
        return mysqli_fetch_assoc($siswa);
    }
}
class Buku {
    protected $isbn;
    protected $judul;
    protected $penulis;
    protected $penerbit;
    protected $tahun_terbit;
    
    public function __construct($isbn, $judul, $penulis, $penerbit, $tahun_terbit)
    {
        $this->isbn = $isbn;
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->penerbit = $penerbit;
        $this->tahun_terbit = $tahun_terbit;
    }
    public function infoBuku()
    {
        return [
            "isbn" => $this->isbn,
            "judul" => $this->judul,
            "penulis" => $this->penulis,
            "penerbit" => $this->penerbit,
            "tahun_terbit" => $this->tahun_terbit
        ];
    }
}
class Pengunjung extends Siswa
{
    protected $nis;
    protected $tanggal_kunjungan;
    public function __construct($nis, $tanggal_kunjungan)
    {
        $this->nis = $nis;
        $this->tanggal_kunjungan = $tanggal_kunjungan;
    }
    public function infoPengunjung()
    {
        return [
            "infosiswa" => $this->infoSiswa(),
            "tanggal_kunjungan" => $this->tanggal_kunjungan
        ];
    }
}

class Peminjam extends Pengunjung
{
    protected array $bukuDipinjam = [];
    private $isbn;

    public function __construct($nama, $waktu, $tanggal, $buku = [])
    {
        parent::__construct($nama, $waktu, $tanggal);

        // Pastikan ISBN dibungkus dengan tanda kutip untuk query SQL
        $isbnList = "'" . implode("','", $buku) . "'";
        $hasilQuery = query("SELECT * FROM buku WHERE isbn IN ($isbnList)");

        // Tambahkan judul buku ke daftar pinjaman
        foreach ($hasilQuery as $data) {
            $this->tambahBuku($data['judul']);
        }
    }

    public function tambahBuku(string $judul)
    {
        $this->bukuDipinjam[] = $judul;
    }

    public function infoBukuPinjaman(): string
    {
        $info = $this->infoPengunjung();
        $info .= " | Jumlah Buku Dipinjam: " . count($this->bukuDipinjam);

        if (!empty($this->bukuDipinjam)) {
            $info .= "\nDaftar Buku:\n";
            foreach ($this->bukuDipinjam as $i => $judul) {
                $info .= ($i + 1) . ". " . $judul . "\n";
            }
        }

        return $info;
    }
}
class Pengembali extends Pengunjung
{
    protected array $bukuDikembalikan = [];
    public function __construct(string $nama, string $waktu, string $tanggal, array $bukuDikembalikan = [])
    {
        parent::__construct($nama, $waktu, $tanggal);
        $this->bukuDikembalikan = $bukuDikembalikan;
    }

    public function infoPengembalian(): string
    {
        $info = $this->infoPengunjung();
        $info .= " | Jumlah Buku Dikembalikan: " . count($this->bukuDikembalikan);
        if (!empty($this->bukuDikembalikan)) {
            $info .= "\nDaftar Buku:\n";
            foreach ($this->bukuDikembalikan as $i => $judul) {
                $info .= ($i + 1) . ". " . $judul . "\n";
            }
        }
        return $info;
    }
}

// $waktu_submit = date("Y-m-d H:i:s");
// var_dump($waktu_submit);
// $person = new peminjam("Sanjaya", "21:49:05", "2025-09-15",);
// $person->tambahBuku("Harry Potter");
// $person->tambahBuku("The Hobbit");
// $person2 = new Pengunjung("Bob", "25", "01");
// echo $person->infopengunjung() . "\n";
// echo $person->infobukupinjaman();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
        --color-kuning: #c7bd02;
        --color-kuning2: #ada400;
    }
        .no-num {
        @apply [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none
    }
    </style>
    <link rel="stylesheet" href="main.css">
    <title>Document</title>
</head>
<body>
    <img class="absolute aspect-square  -top-10 -right-10 size-64" src="./rippedpaper-removebg-preview.png" alt="paper">
    <div class="group absolute top-6 right-6 z-30">
        <img class="aspect-square rounded-full size-15 shadow-white shadow-sm group-hover:scale-125 group-hover:rotate-y-180 group-hover:border-4 duration-300 transition-all" src="https://static.vecteezy.com/system/resources/previews/005/544/718/non_2x/profile-icon-design-free-vector.jpg" alt="">
    </div>
    <div class="box mx-auto absolute max-h-screen max-w-screen z-20 "></div>
    <div class="container p-20 mx-auto relative z-40">
        <h1 class="text-black text-center text-6xl tracking-widest font-extrabold">JAYA PUSTAKA</h1>
        <div class="w-4/10 flex flex-col relative top-20 gap-10 mt-10 bg-amber-500/30 p-10 pt-5 rounded-md">
            <div class="gap-4">
                <h1 class="text-center font-extrabold text-4xl tracking-widest">PENGUNJUNG</h1>
                <hr class="border-3 mx-auto w-40 relative">
            </div>
            <input class="border-3 p-2 font-extrabold tracking-widest text-xl rounded-sm shadow-[4px_4px_0px_0px_#000000] focus:shadow-[8px_8px_0px_0px_#000000] focus:outline-none focus:shadow-kuning2 focus:border-kuning transition-all" type="text" name="nama" placeholder="NAMA LENGKAP" autocomplete="off" id="nama">
            <input class="no-num border-3 p-2 font-extrabold tracking-widest text-xl rounded-sm shadow-[4px_4px_0px_0px_#000000] focus:shadow-[8px_8px_0px_0px_#000000] focus:outline-none focus:shadow-kuning2 focus:border-kuning transition-all" type="number" name="NIS" placeholder="NIS" autocomplete="off" id="nis">
            <input class="border-3 p-2 font-extrabold tracking-widest text-xl rounded-sm shadow-[4px_4px_0px_0px_#000000] focus:shadow-[8px_8px_0px_0px_#000000] focus:outline-none focus:shadow-kuning2 focus:border-kuning transition-all" type="email" name="mail" id="mail" placeholder="EMAIL">
            <input type="button" value="submit" class="bg-kuning hover:bg-black hover:text-white cursor-pointer p-2 font-extrabold tracking-widest text-xl rounded-sm shadow-[4px_4px_0px_0px_#000000] hover:shadow-[8px_8px_0px_0px_#000000] focus:outline-none hover:shadow-kuning2 hover:border-kuning transition-all">
        </div>
    </div>
    <div class="mt-70 p-20">
        <h1>AKTIFITAS PERPUSTAKAAN</h1>
        <table>
            <thead>
                <tr>
                    <th class="border-2 border-black p-2">No</th>
                    <th class="border-2 border-black p-2">nis</th>
                    <th class="border-2 border-black p-2">Nama</th>
                    <th class="border-2 border-black p-2">Tanggal Kunjungan</th>
                    <th class="border-2 border-black p-2">Buku Dipinjam</th>
                    <th class="border-2 border-black p-2">Aksi</th>
                </tr>
            </thead>
            <tbody >
                <?php
                    for($no = 1;$no <= count($data_pengunjung)  ;$no++) : 
                    $nis = $data_pengunjung[$no - 1]['nis'];
                    $buku = query("SELECT * FROM peminjam WHERE nis =". $data_pengunjung[$no - 1]['nis']);
                    $buku = array_column($buku, 'isbn');
                    var_dump($buku);
                    $tanggal_kunjungan = $data_pengunjung[$no - 1]['tanggal_kunjung'];
                    ${"data_" . $no} = new Peminjam($nis, $tanggal_kunjungan, $buku);
                    ?>
                    <tr>
                        <td class="border-2 border-black p-2 text-center"><?= $no ?></td>
                        <td class="border-2 border-black p-2 text-center"><?= ${"data_" . $no}->infoPengunjung()["infosiswa"]["nis"]?></td>
                        <th class="border-2 border-black p-2"><?= ${"data_" . $no}->infoPengunjung()["infosiswa"]["nama"]?></th>
                        <td class="border-2 border-black p-2 text-center"><?= ${"data_" . $no}->infoPengunjung()["tanggal_kunjungan"]?></td>
                        <td class="border-2 border-black p-2 text-center"><?php var_dump(${"data_" . $no}->infoBukuPinjaman())?></td>
                    </tr>
                <?php endfor; ?>
        </table>
    </div>
    <script type="importmap">
        {
    "imports": {
    "three": "https://cdn.jsdelivr.net/npm/three@0.180.0/build/three.module.js",
    "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.180.0/examples/jsm/"
    }
}
</script>
    <script type="module">
        import * as THREE from 'three';
        import {
            OrbitControls
        } from 'three/addons/controls/OrbitControls.js';
        import {
            GLTFLoader
        } from 'three/addons/loaders/GLTFLoader.js';
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({
            alpha: true
        });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.querySelector('.box').appendChild(renderer.domElement);
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enabled = false;
        camera.position.set(-2, 0.2, 4.5);
        scene.add(new THREE.AmbientLight(0xffffff, 1));
        const loader = new GLTFLoader();
        let ketupat;
        loader.load('book/scene.gltf', function(gltf) {
            ketupat = gltf.scene;
            // Responsive scale for mobile, tablet, desktop
            function getScale() {
                const width = window.innerWidth;
                if (width < 640) { // Mobile
                    return 7;
                } else if (width < 1024) { // Tablet
                    return 12;
                } else { // Desktop
                    return 12;
                }
            }
            ketupat.scale.set(getScale(), getScale(), getScale());
            window.addEventListener('resize', () => {
                const s = getScale();
                if (ketupat) ketupat.scale.set(s, s, s);
            });
            ketupat.rotation.z = 0;
            ketupat.rotation.x = 2;
            scene.add(ketupat);
        });
        // Track mouse position
        const mouse = new THREE.Vector2(0, 0);
        window.addEventListener('mousemove', (event) => {
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
        });
        function animate() {
            requestAnimationFrame(animate);
            if (ketupat) {
                // Smooth follow effect
                ketupat.rotation.y += (mouse.x * 0.5 - ketupat.rotation.y) * 0.05;
                ketupat.rotation.x += (mouse.y * 0.3 - ketupat.rotation.x) * 0.05;
            }
            renderer.render(scene, camera);
        }
        animate();
    </script>
    <script>
        var app = {
            chars: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            init: function() {
                app.container = document.createElement('div');
                app.container.className = 'animation-container';
                document.body.appendChild(app.container);
                window.setInterval(app.add, 240);
            },
            add: function() {
                var element = document.createElement('span');
                app.container.appendChild(element);
                app.animate(element);
            },
            animate: function(element) {
                var character = app.chars[Math.floor(Math.random() * app.chars.length)];
                var duration = Math.floor(Math.random() * 15) + 1;
                var offset = Math.floor(Math.random() * (100 - duration * 2)) + 1; // edit 50 as percentage of screen size
                var size = 50 + (100 - duration); // edit 10 and 15 for text size
                element.style.cssText = 'right:' + offset + 'vw; font-size:' + size + 'px;animation-duration:' + duration + 's';
                element.innerHTML = character;
                window.setTimeout(app.remove, duration * 1000, element);
            },
            remove: function(element) {
                element.parentNode.removeChild(element);
            },
        };
        document.addEventListener('DOMContentLoaded', app.init);
    </script>
</body>

</html>