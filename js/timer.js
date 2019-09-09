(function() {
    let buildTimer = () => {
      let start_time, end_time;
      
      let start = () => { start_time = new Date().getTime(); return start_time }
      let stop = () => { end_time = new Date().getTime(); return end_time }
      let get = () => (end_time - start_time) / 1000;
      
      return { start: start, stop: stop, get: get }
    }
    
    window.timerjs = {
      build: buildTimer
    }
  })(window);