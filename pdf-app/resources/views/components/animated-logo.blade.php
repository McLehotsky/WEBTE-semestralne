<style>
  .logo-gradient {
    position: fixed;
    top: 0;
    left: 0;
    width: 200%;
    height: 200%;
    background: linear-gradient(135deg, #fefce8, rgba(251, 146, 60, 0.4));
    z-index: -2; /* najspodnejšia vrstva */
    pointer-events: none;
  }
  .logo-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 200%;
    height: 200%;
/* background-color: rgba(209, 213, 219, 1); */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor'%3E%3Cpath stroke='%23D97706' stroke-linecap='round' stroke-linejoin='round' d='M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z'/%3E%3Cpath stroke='%23FBBF24' stroke-linecap='round' stroke-linejoin='round' d='M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z'/%3E%3C/svg%3E");
    background-repeat: repeat;
    background-size: 80px;
    opacity: 0.2;
    z-index: -1;              /* 👈 pod všetkým */
    pointer-events: none;     /* 👈 neblokuje kliky */
    animation: moveBackground 60s linear infinite;
  }
  @keyframes moveBackground {
    0% {
      transform: translate(0, 0);
    }
    100% {
      transform: translate(-25%, -25%);
    }
  }
</style>

<div class="logo-gradient"></div>
<div class="logo-background"></div>