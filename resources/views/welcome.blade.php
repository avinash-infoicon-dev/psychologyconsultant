<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MindCare - Psychology Consultation</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    font-family: 'Segoe UI', sans-serif;
    background:#f8fbfd;
}

.navbar{
    background:#ffffff;
}

.hero{
    background:linear-gradient(rgba(0,123,255,.08),rgba(0,180,180,.08)),
    url('https://images.unsplash.com/photo-1516302752625-fcc3c50ae61f?auto=format&fit=crop&w=1500&q=80');
    background-size:cover;
    background-position:center;
    min-height:90vh;
    display:flex;
    align-items:center;
}

.hero h1{
    font-size:3.2rem;
    font-weight:700;
}

.hero p{
    font-size:1.2rem;
}

.btn-custom{
    background:#0d6efd;
    color:white;
    padding:12px 30px;
    border-radius:50px;
}

.btn-custom:hover{
    background:#084298;
    color:white;
}

.section-title{
    font-weight:bold;
    color:#0d6efd;
}

.service-card{
    transition:.3s;
    border:none;
    border-radius:20px;
}

.service-card:hover{
    transform:translateY(-10px);
    box-shadow:0 10px 30px rgba(0,0,0,.12);
}

.icon-box{
    width:80px;
    height:80px;
    background:#e8f2ff;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:auto;
    font-size:35px;
    color:#0d6efd;
}

.choose{
    background:#eef8ff;
}

.testimonial{
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.footer{
    background:#0d6efd;
    color:white;
}
</style>

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm sticky-top">
<div class="container">

<a class="navbar-brand fw-bold text-primary" href="#">
<i class="bi bi-heart-pulse-fill"></i> MindCare
</a>

<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="menu">

<ul class="navbar-nav ms-auto">

<li class="nav-item"><a class="nav-link" href="#">Home</a></li>
<li class="nav-item"><a class="nav-link" href="#">About</a></li>
<li class="nav-item"><a class="nav-link" href="#">Services</a></li>
<li class="nav-item"><a class="nav-link" href="#">Psychologists</a></li>
<li class="nav-item"><a class="nav-link" href="#">Contact</a></li>

</ul>

<a href="#" class="btn btn-primary ms-3 rounded-pill">
Book Appointment
</a>

</div>

</div>
</nav>

<!-- Hero -->
<section class="hero">

<div class="container">

<div class="row align-items-center">

<div class="col-lg-6">

<h1>Your Mental Health Matters</h1>

<p class="my-4">
Connect with experienced psychologists, schedule therapy sessions,
and begin your journey toward emotional well-being in a safe and
supportive environment.
</p>

<a href="#" class="btn btn-custom me-3">
Find a Psychologist
</a>

<a href="#" class="btn btn-outline-primary rounded-pill px-4">
Learn More
</a>

</div>

</div>

</div>

</section>

<!-- Services -->
<section class="py-5">

<div class="container">

<h2 class="text-center section-title mb-5">
Our Services
</h2>

<div class="row g-4">

<div class="col-md-4">

<div class="card service-card p-4 text-center">

<div class="icon-box">
<i class="bi bi-person-heart"></i>
</div>

<h4 class="mt-4">Individual Therapy</h4>

<p>
Personalized one-on-one counseling to improve mental well-being.
</p>

</div>

</div>

<div class="col-md-4">

<div class="card service-card p-4 text-center">

<div class="icon-box">
<i class="bi bi-people"></i>
</div>

<h4 class="mt-4">Family Counseling</h4>

<p>
Professional guidance for stronger family relationships and communication.
</p>

</div>

</div>

<div class="col-md-4">

<div class="card service-card p-4 text-center">

<div class="icon-box">
<i class="bi bi-calendar-check"></i>
</div>

<h4 class="mt-4">Online Appointments</h4>

<p>
Consult trusted psychologists securely from the comfort of your home.
</p>

</div>

</div>

</div>

</div>

</section>

<!-- Why Choose -->
<section class="choose py-5">

<div class="container">

<div class="row align-items-center">

<div class="col-lg-6">

<img src="https://images.unsplash.com/photo-1573496799515-eebbb63814f?auto=format&fit=crop&w=800&q=80"
class="img-fluid rounded-4 shadow">

</div>

<div class="col-lg-6">

<h2 class="section-title">
Why Choose MindCare?
</h2>

<p class="mt-4">
Our platform connects patients with licensed psychologists, ensuring
professional support, privacy, and personalized treatment plans.
</p>

<div class="mt-4">

<p><i class="bi bi-check-circle-fill text-success"></i> Certified Psychologists</p>

<p><i class="bi bi-check-circle-fill text-success"></i> Secure Video Consultation</p>

<p><i class="bi bi-check-circle-fill text-success"></i> Confidential Sessions</p>

<p><i class="bi bi-check-circle-fill text-success"></i> Easy Appointment Booking</p>

</div>

</div>

</div>

</div>

</section>

<!-- Appointment -->
<section class="py-5">

<div class="container">

<div class="row">

<div class="col-lg-8 mx-auto">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-body p-5 text-center">

<h2 class="section-title">
Book Your Appointment
</h2>

<p class="mb-4">
Take the first step toward a healthier and happier mind.
</p>

<a href="#" class="btn btn-primary btn-lg rounded-pill">
Schedule Now
</a>

</div>

</div>

</div>

</div>

</div>

</section>

<!-- Testimonials -->
<section class="py-5 bg-light">

<div class="container">

<h2 class="text-center section-title mb-5">
Patient Testimonials
</h2>

<div class="row g-4">

<div class="col-md-4">

<div class="testimonial">

<p>
"The psychologist was compassionate and helped me regain confidence.
Highly recommended!"
</p>

<h5>- Sarah</h5>

</div>

</div>

<div class="col-md-4">

<div class="testimonial">

<p>
"Easy appointment booking and excellent online counseling experience."
</p>

<h5>- Michael</h5>

</div>

</div>

<div class="col-md-4">

<div class="testimonial">

<p>
"I finally found professional support that truly understands my needs."
</p>

<h5>- Emily</h5>

</div>

</div>

</div>

</div>

</section>

<!-- Footer -->
<footer class="footer py-4">

<div class="container">

<div class="row">

<div class="col-md-6">

<h4>MindCare</h4>

<p>
Supporting mental wellness with trusted psychologists and compassionate care.
</p>

</div>

<div class="col-md-6 text-md-end">

<p>
<i class="bi bi-envelope"></i> support@mindcare.com
</p>

<p>
<i class="bi bi-telephone"></i> +1 234 567 890
</p>

<p>
© 2026 MindCare. All Rights Reserved.
</p>

</div>

</div>

</div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>