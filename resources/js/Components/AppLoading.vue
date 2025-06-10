<template>
    <div class="absolute inset-0 z-20 flex items-center justify-center bg-white dark:bg-gray-800">
      <div class="text-xl loader"></div>
    </div>
  </template>
  <style>
  /* HTML: <div class="loader"></div> */
  .loader {
    font-family: monospace;
    display: inline-grid;
  }
  
  .loader:before,
  .loader:after {
    content: 'Loading...';
    grid-area: 1/1;
    -webkit-mask: linear-gradient(90deg, #000 50%, #0000 0) 0 50%/2ch 100%;
    animation: l11 1s infinite cubic-bezier(0.5, 220, 0.5, -220);
  }
  
  .loader:after {
    -webkit-mask-position: 1ch 50%;
    --s: -1;
  }
  
  @keyframes l11 {
    100% {
      transform: translateY(calc(var(--s, 1) * 0.1%));
    }
  }
  </style>
  