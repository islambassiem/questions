$(document).ready(function () {
  let activateBtn = document.getElementById("activate");
  let answers = Array.from(document.getElementById("quiz").children);
  let questtionStmt = document.getElementById("question_stmt");
  let getWinner = document.getElementById("getWinner");

  questtionStmt.style.fontSize = "40px";
  answers[0].style.fontSize = "40px;";
  answers[1].style.fontSize = "40px;";
  answers[2].style.fontSize = "40px;";
  answers[3].style.fontSize = "40px;";

  activateBtn.addEventListener("click", () => {
    let questionId = activateBtn.getAttribute("data-question_id");
    var voice = new Audio ('audio/audio.m4a');
    var horror = new Audio('audio/timer.mp3');
    if(questionId == 35){
      voice.play();
    }else{
      horror.play();
      horror.loop = true;
    }
    fadein();
    answers[0].style.setProperty('--animate-duration', '2s');
    answers[1].style.setProperty('--animate-duration', '3s');
    answers[2].style.setProperty('--animate-duration', '4s');
    answers[3].style.setProperty('--animate-duration', '5s');
    answers.forEach((e) => {
      e.classList.add("animate__zoomIn");
      e.style.backgroundColor = "#1f386b";
      e.style.color = "#fff";
      e.style.fontSize = "30px";
    });
    let timerInterval
    setTimeout(() => {
      Swal.fire({
        position: 'center-center',
        title: 'The question is now active',
        html: 'You have <b></b> seconds to answer.',
        timer: 12000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading()
          const b = Swal.getHtmlContainer().querySelector('b')
          timerInterval = setInterval(() => {
            b.textContent = Math.floor(Swal.getTimerLeft() / 1000)
          }, 1000)
        },
        willClose: () => {
          clearInterval(timerInterval)
        }
      }).then((result) => {
        /* Read more about handling dismissals below */
        if (result.dismiss === Swal.DismissReason.timer) {
          // suspesion##################################
          let timerInterval
          let suspen = new Audio('audio/suspend.mp3');
          horror.pause();
          horror.currentTime = 0
          suspen.play();
          location.reload();
          // suspension#################################
        }
      });
    }, 3000);
    $.ajax({
      url: 'activateQuestion.php',
      method: 'POST',
      data: {question_id: questionId},
      success: function(result) {
          if(result){
            // questtionStmt.classList.remove("bg-danger");
            // questtionStmt.classList.add("bg-info");
            answers.forEach((e) => {
              // e.classList.remove("bg-dark");
              // e.classList.remove("text-white");
              // e.classList.add("bg-success");
              // e.classList.add("text-white");
            });
          }
      } 
    });
  }); 


  getWinner.addEventListener("click", () => {
    let suspen = new Audio('audio/suspend.mp3');
    suspen.play();
    Swal.fire({
      title: 'The winner is ...',
      html: ' <b></b>',
      timer: 6000,
      timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading()
        const b = Swal.getHtmlContainer().querySelector('b')
        timerInterval = setInterval(() => {
          b.textContent = Math.floor(Swal.getTimerLeft() / 1000)
        }, 100)
      },
      willClose: () => {
        clearInterval(timerInterval)
      }
    }).then((result) => {
      /* Read more about handling dismissals below */
      if (result.dismiss === Swal.DismissReason.timer) {
       // Announce the winner
       let WinnerquestionId = getWinner.getAttribute("data-question_id");
       $.ajax({
         url: 'getWinner.php',
         method: 'POST',
         data: {question_id: WinnerquestionId},
         success: function(result) {
          if(result !== 'No one has the right answer'){
           let celeb = new Audio('audio/crowdSheer.mp3');
           celeb.play(); 
           Swal.fire({
             title: result,
             width: 600,
             padding: '3em',
            //  showConfirmButton: false,
            //  timer: 7000,
             color: '#716add',
             background: '#fff url(path/to/photo)',
             backdrop: `
               rgba(0,0,123,0.4)
               url("imgs/descent.gif")
               left top
               round
             `
           });
          }else{
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'No one get it right',
              // footer: '<a href="">Why do I have this issue?</a>'
            })
          }
         } 
       });

       // announce the winner
      }
    });
  });

  function fadein(){
    let intervalId = 0;
    let opacity = 0;
    intervalId = setInterval(show, 200);
  }

  function show(){
    questtionStmt.style.backgroundColor = "#a5dc86";
    opacity = Number(window.getComputedStyle(questtionStmt).getPropertyValue("opacity"));
    if(opacity < 1){
      opacity = opacity + 0.1;
      questtionStmt.style.opacity = opacity;
    }else{
      clearInterval(intervalId);
    }
  }


  $('#lang').select2();
});