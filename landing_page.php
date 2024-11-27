<html>
 <head>
  <title>
   Aplikasi Tabungan Siswa
  </title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
  <style>
   body {
       margin: 0;
       font-family: 'Roboto', sans-serif;
       background-color: #f0f4ff;
   }
   .content {
       display: flex;
       flex-direction: column; /* Mengubah ke kolom untuk layar kecil */
       justify-content: center;
       align-items: center;
       padding: 50px;
       text-align: center;
   }
   .content .text {
       max-width: 500px;
       margin-bottom: 20px; /* Memberi jarak antara teks dan gambar */
   }
   .content .text h1 {
       font-size: 2.5rem; /* Ukuran font responsif */
       color: #1a1a1a;
       margin: 0;
   }
   .content .text h2 {
       font-size: 2rem; /* Ukuran font responsif */
       color: #6c63ff;
       margin: 10px 0;
   }
   .content .text p {
       color: #999;
       line-height: 1.6;
   }
   .content .text .button {
       margin-top: 20px;
   }
   .content .text .button a {
       text-decoration: none;
       padding: 15px 30px;
       border-radius: 30px;
       font-weight: bold;
       background-color: #6c63ff;
       color: #fff;
       display: inline-block;
   }
   .content .text .button a:hover {
       background-color: #5a54d6;
   }
   .content .image {
       max-width: 100%; /* Memastikan gambar tidak lebih besar dari kontainer */
       flex: 1; /* Membuat gambar fleksibel */
   }
   .content .image img {
       width: 100%; /* Gambar akan mengisi lebar kontainer */
       height: auto; /* Memastikan aspek rasio gambar tetap */
   }
   .footer {
       display: flex;
       justify-content: center;
       padding: 20px;
       background-color: #f0f4ff;
   }
   .footer a {
       color: #6c63ff;
       margin: 0 10px;
       font-size: 20px;
   }
   @media (min-width: 600px) {
       .content {
           flex-direction: row; /* Mengubah kembali ke baris untuk layar besar */
       }
       .content .text {
           margin-bottom: 0; /* Menghapus margin bawah pada layar besar */
       }
       .content .image {
           max-width: 50%; /* Gambar akan mengambil setengah dari lebar kontainer */
       }
   }
  </style>
 </head>
 <body>
  <div class="content">
   <div class="text">
    <h2>
     APLIKASI TABUNGAN SISWA
    </h2>
    <h1>
     SIMPANAN UNTUK MASA DEPAN
    </h1>
    <p>
     Dengan aplikasi tabungan siswa, Anda dapat mengelola tabungan dengan lebih mudah dan aman. Mulailah menabung untuk masa depan yang lebih baik!
    </p>
    <div class="button">
     <a href="login.php">MASUK</a> <!-- Tautan ke halaman login -->
    </div>
   </div>
   <div class="image">
    <img alt="Gambar Aplikasi Tabungan" src="a.png" />
   </div>
  </div>
  <div class="footer">
   <a href="#">
    <i class="fab fa-facebook-f"></i>
   </a>
   <a href="#">
    <i class="fab fa-twitter"></i>
   </a>
   <a href="#">
    <i class="fab fa-instagram"></i>
   </a>
  </div>
 </body>
</html>
