window.addEventListener('scroll', function() {
    var div = document.querySelector('.fixed-div');
    var scrollPosition = window.scrollY;
    
    if (scrollPosition >500) {
      div.classList.add('fixage');
    } else {
      div.classList.remove('fixage');
    }
  });
  