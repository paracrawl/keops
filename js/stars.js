document.addEventListener("DOMContentLoaded", () => {
    const max = 5;
    for (let e of document.querySelectorAll(".stars")) {
      let n = parseInt(e.getAttribute("data-stars"));
      let t = max - n;
      for (let i = 0; i < n; i++) {
        let star = document.createElement('div');
        star.classList.add('star');
        star.classList.add('star-filled');
        e.appendChild(star);
      }
      
      for (let i = 0; i < t; i++) {
        let star = document.createElement('div');
        star.classList.add('star-empty');
        star.classList.add('star');
        e.appendChild(star);
      }
    }
  });