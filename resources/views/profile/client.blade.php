@extends('layouts.app') 

@section('content')

<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hồ Sơ Cá Nhân - BookBee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <style>
    :root {
      --primary: #6366f1;
      --primary-hover: #4f46e5;
      --secondary: #a855f7;
      --background: #f0f2f5;
      --surface: #ffffff;
      --text: #374151;
      --text-light: #6b7280;
      --border: #e5e7eb;
      --shadow: rgba(0,0,0,0.05);
    }

    [data-theme="dark"] {
      --primary: #818cf8;
      --primary-hover: #6366f1;
      --secondary: #c084fc;
      --background: #1f2937;
      --surface: #293548;
      --text: #f3f4f6;
      --text-light: #d1d5db;
      --border: #374151;
      --shadow: rgba(0,0,0,0.2);
    }

    /* Add glass morphism effect */
    .glass {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    [data-theme="dark"] .glass {
      background: rgba(0, 0, 0, 0.2);
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--background);
      min-height: 100vh;
      color: var(--text);
      transition: all 0.3s ease;
    }

    /* Modern 3D Card Effect */
    .card-3d {
      transform-style: preserve-3d;
      transform: perspective(1000px);
    }

    .card-3d:hover {
      transform: perspective(1000px) rotateX(5deg) rotateY(5deg);
    }

    /* Floating Animation */
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }

    .float {
      animation: float 3s ease-in-out infinite;
    }

    /* Theme Switcher */
    .theme-switch {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1000;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 50%;
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 4px 20px var(--shadow);
      transition: all 0.3s ease;
    }

    .theme-switch:hover {
      transform: scale(1.1);
    }

    /* Enhanced Navigation */
    .navbar-custom {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      padding: 1rem 0;
      box-shadow: 0 2px 15px var(--shadow);
      position: sticky;
      top: 0;
      z-index: 1000;
      backdrop-filter: blur(10px);
    }

    /* Enhanced Sidebar */
    .sidebar {
      background: var(--surface);
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 4px 20px var(--shadow);
      position: sticky;
      top: 100px;
      transition: all 0.3s ease;
    }

    .sidebar a {
      color: var(--text);
      background: linear-gradient(to right, transparent 50%, var(--primary) 50%);
      background-size: 200% 100%;
      background-position: left bottom;
      transition: all 0.3s ease;
    }

    .sidebar a:hover {
      background-position: right bottom;
      color: white;
      transform: translateX(10px);
    }

    /* Enhanced Form Controls */
    .form-control, .form-select {
      background: var(--surface);
      border: 1.5px solid var(--border);
      color: var(--text);
      transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
      transform: translateY(-2px);
    }

    /* Enhanced Avatar Section */
    .avatar {
      background: linear-gradient(135deg, 
        rgba(var(--primary-rgb), 0.1) 0%, 
        rgba(var(--secondary-rgb), 0.1) 100%);
      border-radius: 20px;
      padding: 30px;
      transition: all 0.3s ease;
    }

    .avatar:hover {
      transform: translateY(-5px);
    }

    .avatar img {
      border: none;
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      padding: 5px;
      transition: all 0.5s ease;
    }

    .avatar img:hover {
      transform: scale(1.1) rotate(10deg);
    }

    /* Animated Button */
    .btn-save {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      border: none;
      padding: 15px 40px;
      color: white;
      border-radius: 12px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .btn-save::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(
        to bottom right,
        rgba(255,255,255,0.2) 0%,
        rgba(255,255,255,0.2) 40%,
        rgba(255,255,255,0.6) 50%,
        rgba(255,255,255,0.2) 60%,
        rgba(255,255,255,0.2) 100%
      );
      transform: rotate(45deg);
      transition: 0.5s;
      opacity: 0;
    }

    .btn-save:hover::after {
      opacity: 1;
      transform: rotate(45deg) translate(50%, 50%);
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 10px;
    }

    ::-webkit-scrollbar-track {
      background: var(--background);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: var(--primary-hover);
    }

    /* Loading Animation */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(var(--background-rgb), 0.8);
      backdrop-filter: blur(5px);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .loading-spinner {
      width: 50px;
      height: 50px;
      border: 3px solid var(--border);
      border-top: 3px solid var(--primary);
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    /* Ripple Effect */
    .ripple {
      position: absolute;
      border-radius: 50%;
      transform: scale(0);
      animation: ripple 0.6s linear;
      background-color: rgba(255, 255, 255, 0.7);
    }

    @keyframes ripple {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }

    /* Toast Notifications */
    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 30px;
      background: var(--surface);
      border-left: 4px solid var(--primary);
      border-radius: 4px;
      box-shadow: 0 4px 20px var(--shadow);
      transform: translateX(120%);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .toast.show {
      transform: translateX(0);
    }

    /* Progress Indicator */
    .progress-bar {
      position: fixed;
      top: 0;
      left: 0;
      height: 3px;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      transition: width 0.3s ease;
    }
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f0f2f5;
      min-height: 100vh;
    }
    .navbar-custom {
      background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
      padding: 1rem 0;
      box-shadow: 0 2px 15px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
      backdrop-filter: blur(10px);
    }
    .navbar-custom .navbar-brand {
      color: #fff;
      font-weight: 700;
      font-size: 1.5rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      position: relative;
      overflow: hidden;
    }
    .navbar-custom .navbar-brand::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 2px;
      background: #fff;
      transform: scaleX(0);
      transform-origin: right;
      transition: transform 0.3s ease;
    }
    .navbar-custom .navbar-brand:hover::after {
      transform: scaleX(1);
      transform-origin: left;
    }
    .navbar-custom .nav-link {
      color: #fff;
      font-weight: 500;
      padding: 0.5rem 1rem;
      position: relative;
      transition: all 0.3s ease;
    }
    .navbar-custom .nav-link::before {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      width: 0;
      height: 2px;
      background: #fff;
      transition: all 0.3s ease;
    }
    .navbar-custom .nav-link:hover::before {
      width: 80%;
      left: 10%;
    }
    .navbar-custom .nav-link:hover {
      transform: translateY(-2px);
    }
    .sidebar {
      min-width: 250px;
      background: white;
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      position: sticky;
      top: 60px;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      margin-bottom: 8px;
      color: #4b5563;
      text-decoration: none;
      border-radius: 10px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .sidebar a::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 3px;
      background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
      transform: scaleY(0);
      transition: transform 0.3s ease;
    }
    .sidebar a:hover::before {
      transform: scaleY(1);
    }
    .sidebar a i {
      margin-right: 12px;
      font-size: 1.1rem;
      transition: transform 0.3s ease;
    }
    .sidebar a:hover {
      background: #f3f4f6;
      color: #6366f1;
      transform: translateX(5px);
    }
    .sidebar a:hover i {
      transform: scale(1.2);
    }
    .main-content {
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .form-control, .form-select {
      border-radius: 10px;
      border: 1.5px solid #e5e7eb;
      padding: 12px;
      transition: all 0.3s ease;
      background: #fff;
    }
    .form-control:focus, .form-select:focus {
      border-color: #6366f1;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
      transform: translateY(-1px);
    }
    .form-control::placeholder {
      color: #9ca3af;
    }
    .form-label {
      font-weight: 500;
      color: #374151;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .form-label i {
      color: #6366f1;
      font-size: 0.9em;
    }
    .avatar {
      position: relative;
      padding: 12px;
      background: 
        linear-gradient(145deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.1) 100%),
        linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
      border-radius: 12px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(15px);
      box-shadow: 
        0 3px 12px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      overflow: hidden;
    }
    .avatar::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
      );
      transition: left 0.8s ease;
    }
    .avatar:hover::before {
      left: 100%;
    }
    .avatar:hover {
      transform: translateY(-6px);
      box-shadow: 
        0 15px 35px rgba(99, 102, 241, 0.2),
        0 8px 16px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }
    .avatar-container {
      position: relative;
      display: inline-block;
      margin-bottom: 15px;
    }
    .avatar-container::before {
      content: '';
      position: absolute;
      top: -6px;
      left: -6px;
      right: -6px;
      bottom: -6px;
      background: linear-gradient(45deg, #6366f1, #a855f7, #10b981, #f59e0b);
      border-radius: 50%;
      z-index: -1;
      animation: rotate 10s linear infinite;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .avatar-container:hover::before {
      opacity: 0.5;
    }
    @keyframes rotate {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .avatar-image {
      width: 110px;
      height: 110px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #fff;
      padding: 3px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 
        0 6px 20px rgba(99, 102, 241, 0.2),
        0 2px 6px rgba(0, 0, 0, 0.06),
        inset 0 0 0 1px rgba(99, 102, 241, 0.08);
      background: linear-gradient(135deg, #6366f1, #a855f7);
      position: relative;
      z-index: 2;
    }
    .avatar-image::before {
      content: '';
      position: absolute;
      top: -6px;
      left: -6px;
      right: -6px;
      bottom: -6px;
      background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.5) 50%, transparent 70%);
      border-radius: 50%;
      z-index: -1;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .avatar-image:hover::before {
      opacity: 1;
      animation: shine 1.5s ease-in-out;
    }
    @keyframes shine {
      0% { transform: scale(0.8) rotate(0deg); opacity: 0; }
      50% { opacity: 1; }
      100% { transform: scale(1.2) rotate(180deg); opacity: 0; }
    }
    .avatar-image:hover {
      transform: scale(1.05) rotateZ(3deg);
      box-shadow: 
        0 12px 32px rgba(99, 102, 241, 0.3),
        0 6px 12px rgba(0, 0, 0, 0.12),
        inset 0 0 0 2px rgba(99, 102, 241, 0.15);
    }
    .avatar-badge {
      position: absolute;
      bottom: 8px;
      right: 8px;
      width: 26px;
      height: 26px;
      background: linear-gradient(135deg, #10b981, #059669);
      border-radius: 50%;
      border: 2px solid #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 
        0 3px 10px rgba(16, 185, 129, 0.25),
        0 0 0 1px rgba(16, 185, 129, 0.08);
      animation: heartbeat 2s infinite;
      z-index: 3;
      transition: all 0.3s ease;
    }
    .avatar-badge::before {
      content: '';
      position: absolute;
      top: -6px;
      left: -6px;
      right: -6px;
      bottom: -6px;
      background: linear-gradient(45deg, #10b981, #059669);
      border-radius: 50%;
      z-index: -1;
      opacity: 0;
      animation: pulse-ring 2s infinite;
    }
    .avatar-badge i {
      color: white;
      font-size: 10px;
      text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    @keyframes heartbeat {
      0%, 40%, 80%, 100% { transform: scale(1); }
      20%, 60% { transform: scale(1.15); }
    }
    @keyframes pulse-ring {
      0% { transform: scale(0.8); opacity: 1; }
      100% { transform: scale(2); opacity: 0; }
    }
    .upload-section {
      margin-top: 15px;
      padding: 15px;
      background: 
        linear-gradient(145deg, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0.1) 100%),
        linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%);
      border-radius: 12px;
      border: 2px dashed rgba(99, 102, 241, 0.3);
      backdrop-filter: blur(8px);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    .upload-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        90deg,
        transparent,
        rgba(99, 102, 241, 0.1),
        transparent
      );
      transition: left 0.6s ease;
    }
    .upload-section:hover::before {
      left: 100%;
    }
    .upload-section:hover {
      border-color: rgba(99, 102, 241, 0.6);
      background: 
        linear-gradient(145deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.2) 100%),
        linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(168, 85, 247, 0.08) 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(99, 102, 241, 0.12);
    }
    .upload-section.dragover {
      border-color: #6366f1;
      background: 
        linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.3) 100%),
        linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(168, 85, 247, 0.12) 100%);
      transform: scale(1.01) translateY(-3px);
      box-shadow: 
        0 10px 25px rgba(99, 102, 241, 0.18),
        inset 0 0 0 1px rgba(99, 102, 241, 0.15);
    }
    .upload-btn {
      position: relative;
      overflow: hidden;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border-radius: 12px;
    }
    .upload-btn input[type="file"] {
      position: absolute;
      left: -9999px;
      opacity: 0;
    }
    .upload-btn-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      padding: 12px 16px;
      background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
      color: white;
      border-radius: 10px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    .upload-btn-content::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
      );
      transition: left 0.6s ease;
    }
    .upload-btn:hover .upload-btn-content::before {
      left: 100%;
    }
    .upload-btn:hover .upload-btn-content {
      transform: translateY(-2px) scale(1.01);
      box-shadow: 
        0 8px 20px rgba(99, 102, 241, 0.3),
        0 4px 8px rgba(0, 0, 0, 0.08);
      background: linear-gradient(135deg, #4f46e5 0%, #9333ea 50%, #e11d48 100%);
    }
    .upload-btn-content i {
      font-size: 16px;
      animation: bounce 2s infinite;
    }
    .upload-btn-content span {
      font-weight: 600;
      font-size: 12px;
      text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
      40% { transform: translateY(-5px); }
      60% { transform: translateY(-2px); }
    }
    .particles {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
    }
    .particles::before,
    .particles::after {
      content: '';
      position: absolute;
      width: 4px;
      height: 4px;
      background: linear-gradient(45deg, #6366f1, #a855f7);
      border-radius: 50%;
      animation: float-particles 6s infinite linear;
    }
    .particles::before {
      top: 20%;
      left: 10%;
      animation-delay: 0s;
    }
    .particles::after {
      top: 60%;
      right: 15%;
      animation-delay: 3s;
    }
    @keyframes float-particles {
      0%, 100% { opacity: 0; transform: translateY(0) scale(0); }
      50% { opacity: 1; transform: translateY(-50px) scale(1); }
    }
    .avatar-glow {
      position: absolute;
      top: -20px;
      left: -20px;
      right: -20px;
      bottom: -20px;
      background: linear-gradient(45deg, #6366f1, #a855f7, #ec4899, #10b981);
      border-radius: 50%;
      z-index: -1;
      opacity: 0;
      filter: blur(20px);
      animation: glow-pulse 4s ease-in-out infinite;
    }
    @keyframes glow-pulse {
      0%, 100% { opacity: 0; transform: scale(0.8); }
      50% { opacity: 0.6; transform: scale(1.2); }
    }
    .avatar-ring {
      position: absolute;
      top: -15px;
      left: -15px;
      right: -15px;
      bottom: -15px;
      border: 2px solid transparent;
      border-radius: 50%;
      background: linear-gradient(45deg, #6366f1, #a855f7) border-box;
      mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
      mask-composite: exclude;
      animation: spin-ring 8s linear infinite;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .avatar-container:hover .avatar-ring {
      opacity: 1;
    }
    @keyframes spin-ring {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .upload-info {
      text-align: center;
      margin-top: 10px;
      position: relative;
    }
    .upload-info .text-muted {
      font-size: 10px;
      color: #6b7280;
      background: rgba(255, 255, 255, 0.7);
      padding: 4px 8px;
      border-radius: 12px;
      backdrop-filter: blur(4px);
      border: 1px solid rgba(99, 102, 241, 0.08);
    }
    .btn-save {
      background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
      border: none;
      padding: 12px 30px;
      font-weight: 600;
      color: white;
      border-radius: 10px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .btn-save::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        to right,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
      );
      transition: 0.5s;
    }
    .btn-save:hover::before {
      left: 100%;
    }
    .btn-save:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }
    .disabled-input {
      background-color: #f3f4f6;
      cursor: not-allowed;
      opacity: 0.8;
    }
    @media (max-width: 768px) {
      .container {
        padding: 15px;
      }
      .sidebar {
        margin-bottom: 15px;
        position: static;
      }
      .main-content {
        padding: 20px;
      }
      .avatar {
        margin-top: 15px;
      }
      .navbar-custom {
        padding: 0.5rem 0;
      }
      .navbar-custom .navbar-brand {
        font-size: 1.2rem;
      }
    }
    // Add loading animation
    .loading {
      position: relative;
    }
    .loading::after {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      background: rgba(255, 255, 255, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(5px);
    }
    .loading::before {
      content: '';
      position: absolute;
      width: 40px;
      height: 40px;
      top: 50%;
      left: 50%;
      margin: -20px 0 0 -20px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #6366f1;
      border-radius: 50%;
      z-index: 1;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>

<body>
  <div class="progress-bar" style="width: 0%"></div>
  <div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <div class="row g-4">
      <div class="col-md-3">
        <div class="sidebar">
          <a href="#"><i class="fas fa-user"></i>Hồ Sơ Của Tôi</a>
                          <a href="{{ route('account.addresses') }}">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Quản lý địa chỉ</span>
                </a>
          <a href="{{ url('/account/password/change') }}"><i class="fas fa-key"></i>Đổi Mật Khẩu</a>
        </div>
      </div>
      <div class="col-md-9">
        <div class="main-content">
            <h2 class="mb-3">Hồ Sơ Của Tôi</h2>
            <p class="text-muted mb-4">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>

            <form method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Đặt phương thức PUT để update -->

            <div class="row">
                <!-- Avatar hiển thị -->
                <div class="col-md-4 text-center avatar">
                    <div class="particles"></div>
                    <div class="avatar-container">
                        <div class="avatar-glow"></div>
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=200' }}" 
                             alt="Avatar" class="avatar-image">
                        <div class="avatar-badge">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="avatar-ring"></div>
                    </div>
                    
                    <!-- Upload ảnh mới -->
                    <div class="upload-section">
                        <div class="upload-btn" onclick="document.getElementById('avatar-input').click()">
                            <input type="file" name="avatar" id="avatar-input" accept="image/jpeg,image/png" style="display: none;">
                            <div class="upload-btn-content">
                                <i class="fas fa-camera"></i>
                                <span>Chọn ảnh</span>
                            </div>
                        </div>
                        <div class="upload-info">
                            <p class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Tối đa 1MB. Định dạng: .JPEG, .PNG
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                <!-- Tên đăng nhập -->
                <div class="mb-4">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required>
                </div>

                <!-- Số điện thoại -->
                <div class="mb-4">
                    <label class="form-label">Số điện thoại</label>
                    <input type="tel" class="form-control" name="phone" value="{{ Auth::user()->phone }}">
                </div>

                <!-- Nút lưu -->
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                </button>
                </div>

            </div>
            </form>
        </div>
        </div>
    </div>
  </div>

  <div class="theme-switch">
    <i class="fas fa-moon"></i>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Theme Switching
    const themeSwitch = document.querySelector('.theme-switch');
    themeSwitch.addEventListener('click', () => {
      const html = document.documentElement;
      const currentTheme = html.getAttribute('data-theme');
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      
      html.setAttribute('data-theme', newTheme);
      themeSwitch.innerHTML = `<i class="fas fa-${newTheme === 'light' ? 'moon' : 'sun'}"></i>`;
      
      localStorage.setItem('theme', newTheme);
    });

    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    themeSwitch.innerHTML = `<i class="fas fa-${savedTheme === 'light' ? 'moon' : 'sun'}"></i>`;

    // Progress bar
    const progressBar = document.querySelector('.progress-bar');
    window.addEventListener('scroll', () => {
      const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
      const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      const scrolled = (winScroll / height) * 100;
      progressBar.style.width = scrolled + '%';
    });

    // Enhanced form validation and submission
    function luuHoSo() {
      const form = document.querySelector('.main-content');
      const ten = document.getElementById('ten').value;
      const required = ['ten', 'gioiTinh', 'ngay', 'thang', 'nam'];
      let isValid = true;
      let errors = [];

      required.forEach(field => {
        const input = document.getElementById(field);
        if (!input?.value) {
          isValid = false;
          errors.push(`Vui lòng nhập ${field.replace(/([A-Z])/g, ' $1').toLowerCase()}`);
          input?.classList.add('is-invalid');
        } else {
          input?.classList.remove('is-invalid');
        }
      });

      if (!isValid) {
        Swal.fire({
          icon: 'error',
          title: 'Vui lòng kiểm tra lại',
          html: errors.join('<br>'),
          confirmButtonColor: getComputedStyle(document.documentElement)
            .getPropertyValue('--primary')
        });
        return;
      }

      // Show loading
      form.classList.add('loading');
      
      // Simulate API call
      setTimeout(() => {
        form.classList.remove('loading');
        
        Swal.fire({
          icon: 'success',
          title: 'Thành công!',
          text: 'Thông tin hồ sơ đã được cập nhật',
          showConfirmButton: false,
          timer: 1500,
          backdrop: `
            rgba(var(--primary-rgb), 0.1)
            url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0C13.432 0 0 13.432 0 30c0 16.568 13.432 30 30 30 16.568 0 30-13.432 30-30C60 13.432 46.568 0 30 0zm0 54C16.745 54 6 43.255 6 30S16.745 6 30 6s24 10.745 24 24-10.745 24-24 24zm12.844-33.755l-15.879 15.88-9.808-9.808 4.242-4.243 5.566 5.566 11.637-11.637 4.242 4.242z' fill='%236366f1'/%3E%3C/svg%3E")
            center center no-repeat
          `
        });

        // Show toast notification
        showToast('Thông tin hồ sơ đã được cập nhật!');
      }, 1500);
    }

    // Toast notification
    function showToast(message) {
      const toast = document.createElement('div');
      toast.className = 'toast';
      toast.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        ${message}
      `;
      document.body.appendChild(toast);
      
      requestAnimationFrame(() => {
        toast.classList.add('show');
      });

      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }

    // Enhanced ripple effect
    document.querySelectorAll('.btn-save').forEach(button => {
      button.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const ripple = document.createElement('div');
        ripple.className = 'ripple';
        ripple.style.width = ripple.style.height = `${Math.max(rect.width, rect.height)}px`;
        ripple.style.left = `${e.clientX - rect.left - ripple.offsetWidth/2}px`;
        ripple.style.top = `${e.clientY - rect.top - ripple.offsetHeight/2}px`;
        
        this.appendChild(ripple);
        ripple.addEventListener('animationend', () => ripple.remove());
      });
    });

    // Create floating particles
    function createParticles() {
      const avatarContainer = document.querySelector('.avatar');
      if (!avatarContainer) return;
      
      const particles = avatarContainer.querySelector('.particles');
      
      for (let i = 0; i < 8; i++) {
        const particle = document.createElement('div');
        particle.style.cssText = `
          position: absolute;
          width: ${Math.random() * 6 + 2}px;
          height: ${Math.random() * 6 + 2}px;
          background: linear-gradient(45deg, #6366f1, #a855f7);
          border-radius: 50%;
          left: ${Math.random() * 100}%;
          top: ${Math.random() * 100}%;
          animation: float-particles ${Math.random() * 4 + 4}s infinite linear;
          animation-delay: ${Math.random() * 2}s;
          opacity: 0;
        `;
        particles.appendChild(particle);
      }
    }

    // Initialize particles when page loads
    document.addEventListener('DOMContentLoaded', createParticles);

    // Image preview with enhanced functionality
    const imageInput = document.getElementById('avatar-input');
    const previewImage = document.querySelector('.avatar-image');
    const uploadSection = document.querySelector('.upload-section');
    const avatarContainer = document.querySelector('.avatar-container');
    
    // File input change handler
    imageInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        // Validate file size (1MB = 1048576 bytes)
        if (file.size > 1048576) {
          Swal.fire({
            icon: 'error',
            title: 'File quá lớn!',
            text: 'Vui lòng chọn file có kích thước nhỏ hơn 1MB',
            confirmButtonColor: '#6366f1'
          });
          this.value = '';
          return;
        }
        
        // Validate file type
        if (!file.type.match(/^image\/(jpeg|jpg|png)$/)) {
          Swal.fire({
            icon: 'error',
            title: 'Định dạng không hợp lệ!',
            text: 'Chỉ chấp nhận file .JPEG, .JPG, .PNG',
            confirmButtonColor: '#6366f1'
          });
          this.value = '';
          return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImage.src = e.target.result;
          
          // Add loading effect
          previewImage.style.opacity = '0.5';
          previewImage.style.transform = 'scale(0.9)';
          
          setTimeout(() => {
            previewImage.style.opacity = '1';
            previewImage.style.transform = 'scale(1.05)';
            
            // Add success effect
            avatarContainer.style.animation = 'none';
            avatarContainer.offsetHeight; // Trigger reflow
            avatarContainer.style.animation = 'success-bounce 0.6s ease';
            
            setTimeout(() => {
              previewImage.style.transform = 'scale(1)';
            }, 300);
          }, 200);
          
          // Show success toast
          showToast('✨ Ảnh đã được chọn! Nhấn "Lưu thay đổi" để cập nhật.');
        };
        reader.readAsDataURL(file);
      }
    });

    // Add success animation keyframes
    const style = document.createElement('style');
    style.textContent = `
      @keyframes success-bounce {
        0% { transform: scale(1); }
        50% { transform: scale(1.1) rotateZ(5deg); }
        100% { transform: scale(1) rotateZ(0deg); }
      }
    `;
    document.head.appendChild(style);
      }
    });

    // Drag and drop functionality
    uploadSection.addEventListener('dragover', function(e) {
      e.preventDefault();
      this.classList.add('dragover');
    });

    uploadSection.addEventListener('dragleave', function(e) {
      e.preventDefault();
      this.classList.remove('dragover');
    });

    uploadSection.addEventListener('drop', function(e) {
      e.preventDefault();
      this.classList.remove('dragover');
      
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        imageInput.files = files;
        imageInput.dispatchEvent(new Event('change'));
      }
    });

    // Smooth scroll with enhanced behavior
    document.querySelectorAll('.sidebar a').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const targetId = this.getAttribute('href').substring(1);
        const target = document.getElementById(targetId);
        
        if (target) {
          const targetPosition = target.getBoundingClientRect().top + window.pageYOffset;
          const startPosition = window.pageYOffset;
          const distance = targetPosition - startPosition;
          const duration = 1000;
          let start = null;
          
          window.requestAnimationFrame(step);

          function step(timestamp) {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            const percentage = Math.min(progress / duration, 1);
            
            window.scrollTo(0, startPosition + distance * easeInOutCubic(percentage));
            
            if (progress < duration) {
              window.requestAnimationFrame(step);
            }
          }
        }
      });
    });

    // Easing function
    function easeInOutCubic(t) {
      return t < 0.5 
        ? 4 * t * t * t 
        : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  

</body>
</html>
@endsection
