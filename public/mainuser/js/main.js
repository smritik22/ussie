// Language toogle
function myFunction() {
    document.getElementById("lngDropdown").classList.toggle("show");
  }
  
  // Close the dropdown if the user clicks outside of it
  window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
      var dropdowns = document.getElementsByClassName("dropdown-content");
      var i;
      for (i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show')) {
          openDropdown.classList.remove('show');
        }
      }
    }
  }

// Profile toogle
function profileFunction() {
  document.getElementById("profileDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
// Date toogle
function dateFunction() {
  document.getElementById("dateDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}


// Smooth scroll----------------------------------------
function scrollNav() {
  $('#main-nav a').click(function(){
    $(".active").removeClass("active");     
    $(this).addClass("active");
    
    $('html, body').stop().animate({
      scrollTop: $($(this).attr('href')).offset().top - 160
    }, 300);
    return false;
  });
}
scrollNav();

// Navigation Menu [ Without Submenu ] -------------------------------------------------------------
		
$('h4.menuToggle').on("click",function(){
	if ($(window).width() < 992 ) {
		$('.nav-menu').slideToggle();
	}
		$('.nav-menu').toggleClass('toggled-on');
});

// Resize Function

$(window).on("load resize",function(e){
  if ($(window).width() > 991 ) {
    $(".nav-menu").show();
  }
  else {
    $(".nav-menu").hide();
  }
});	

// SEARCH TOGGLE-------------------------------------------
// Search Responsive
jQuery('.search-icon-mb').on("click",function(){
  if (jQuery(window).width()) {
    jQuery('.dropdownBox').slideToggle();
  }
  jQuery('.dropdownBox').toggleClass('toggled-on');
});


// DASHBOARD SIDEBAR-------------------------------------------
$('.sidebarIconToggle').click( function() {
  $("#sidebarMenu").toggleClass("someClass");
} );

// EXPAND DOC LIST IN HOME PAGE--------------------------
$('.clinics').find('a[href="#"]').on('click', function (e) {
  e.preventDefault();
  this.expand = !this.expand;
  $(this).text(this.expand?"View Less":"View All");
  $(this).closest('.clinics').find('.less, .expand').toggleClass('less expand');
});


// screenshot slider -----------------------------------------------------
$('.doc-slider').slick({
    infinite: false,
    slidesToShow: 4,
    arrows:true,
    responsive: [
        {
            breakpoint: 1400,
            settings: {
              arrows: true,
              slidesToShow: 4
            }
        },  
      {
        breakpoint: 1000,
        settings: {
          arrows: true,
          slidesToShow: 3
        }
      },  
      {
        breakpoint: 640,
        settings: {
          arrows: true,
          slidesToShow: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          arrows: true,
          slidesToShow: 1
        }
      }
    ]
  });

  // purchase coins
    $(".choose-log-cat input[type='radio']").click(function(){
      $(".fill-details").addClass("active");      
  });

// custom file ------------------------------------------  
  var input = document.getElementById( 'file-upload' );
  var infoArea = document.getElementById( 'file-upload-filename' );
  
  input.addEventListener( 'change', showFileName );
  
  function showFileName( event ) {
    
    // the change event gives us the input it occurred in 
    var input = event.srcElement;
    
    // the input has an array of files in the `files` property, each one has a name that you can use. We're just using the name here.
    var fileName = input.files[0].name;
    
    // use fileName however fits your app best, i.e. add it into a div
    infoArea.textContent = 'File name: ' + fileName;
  }

// custom file ------------------------------------------

// var input = document.querySelector('input[type=file]'); // see Example 4

// input.onchange = function () {
// var file = input.files[0];

// drawOnCanvas(file);   // see Example 6
// displayAsImage(file); // see Example 7
// };

// function drawOnCanvas(file) {
// var reader = new FileReader();

// reader.onload = function (e) {
// var dataURL = e.target.result,
//   c = document.querySelector('canvas'), // see Example 4
//   ctx = c.getContext('2d'),
//   img = new Image();

// img.onload = function() {
// c.width = img.width;
// c.height = img.height;
// ctx.drawImage(img, 0, 0);
// };

// img.src = dataURL;
// };

// reader.readAsDataURL(file);
// }

// $("#upfile1").click(function () {
// $("#file1").trigger('click');
// });
// $("#upfile2").click(function () {
//   $("#file2").trigger('click');
//   });


// function Toggle() { 
//   var temp = document.getElementById("password-log"); 
//   if (temp.type === "password") { 
//       temp.type = "text"; 
//   } 
//   else { 
//       temp.type = "password"; 
//   }
//   var temp1 = document.getElementById("password-reg"); 
//   if (temp1.type === "password") { 
//       temp1.type = "text"; 
//   } 
//   else { 
//       temp1.type = "password"; 
//   } 
  
