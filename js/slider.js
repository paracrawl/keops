(function() {
  let parent, control, handle, controloffset, controllimit;
  let moving = false, offset = 0;
  
  function initSlider(element) {
    if (typeof element == "string") element = document.querySelector(element);

    parent = element;
    control = element.querySelector('.custom-range-control');
    handle = element.querySelector('.handle');
    controloffset = element.getBoundingClientRect().left;
    controllimit = element.getBoundingClientRect().right - controloffset - handle.offsetWidth;

    let initstep = element.querySelector('input').value;
    if (initstep) moveHandle(initstep);

    let handledown = (e) => {
      if (e.target.nodeName !== 'INPUT') e.preventDefault()
      if (e.touches) e = e.touches[0];

      if (!e.target.classList.contains("handle")) {
        offset = e.clientX - controloffset - (handle.offsetWidth/2);
        moveHandle();
      }

      moving = true;
      handle.classList.toggle('active');
    }

    control.addEventListener("mousedown", function(e) { handledown(e) });
    control.addEventListener("touchstart", function(e) { handledown(e) });

    let handlemove = (e) => {
      e.preventDefault();
      if (e.touches) e = e.touches[0];

      if (moving) {
        offset = e.clientX - controloffset - (handle.offsetWidth/2);
        moveHandle();
      }
    }

    document.addEventListener("mousemove", function(e) { handlemove(e); });
    document.addEventListener("touchmove", function(e) { handlemove(e); });

    let handleup = (e) => {
      if (moving) handle.classList.toggle('active');
      moving = false;
    }

    document.addEventListener("mouseup", function(e) { handleup(e) });
    document.addEventListener("touchend", function(e) { handleup(e) });

    window.addEventListener('resize', function() {
      controloffset = element.getBoundingClientRect().left;
      controllimit = element.getBoundingClientRect().right - controloffset - handle.offsetWidth;
      moveHandle(parent.querySelector('input').value);
    });

    return {
      move: moveHandle,

      step: () => {
        return parent.querySelector('input').value
      },

      back: (step) => {
        let value = parseInt(parent.querySelector('input').value) - step;
        moveHandle(value);
      },

      forward: (step) => {
        let value = parseInt(parent.querySelector('input').value) + step;
        moveHandle(value);
      }
    }
  }

  function moveHandle(step = null) {
    console.log('Move', step);
    if (step != null) {
      step = parseInt(step);
      offset = (step / 100) * (controllimit);
    }

    if (offset < 0) offset = 0;
    if (offset > controllimit) offset = controllimit;
    
    handle.setAttribute('style', `left: ${offset}px`);
    control.querySelector('.pre').setAttribute('style', `width: ${offset}px`);
    control.querySelector('.post').setAttribute('style', `width: ${(control.offsetWidth - handle.offsetWidth - offset)}px`);
  
    let value = (step) ? step : Math.ceil(((offset) / (control.offsetWidth - handle.offsetWidth)) * 100);
    parent.querySelector('input').value = value;
  }
    
  window.initSlider = initSlider;
})(window);