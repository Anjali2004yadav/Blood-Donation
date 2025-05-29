const left = document.querySelector('.left');
const right = document.querySelector('.right');
const slider = document.querySelector('.slider');
const images = document.querySelectorAll('.image');
let slideNumber = 1;
let length = images.length

//step;2
//buttton start dots
//for button 
//button ko select kiya 
const button=document.querySelector('.bottom');

 //for loop use kara ga jo  i=0 and (i<length sevhota raha ga tab tak run ho ga loop )
for(i=0; i<length; i++){
  //const div  ko eak variablema storekara ga 
   const div=document.createElement('div');
   //div button ko selectkarga
   div.className='button';
   // jo div button ki class hai use ka under save ho ga 
   button.appendChild(div);
};
// sab button ko buttons var ma store kar liya
const  buttons = document.querySelectorAll('.button');
buttons[0].style.backgroundColor = 'white';//style bg in button 
//dot pe click karta hi white se transprant color ho jay
const resetBg=()=>{
  //1
  buttons.forEach((button)=>{
    button.style.backgroundColor='transparent';
    //stop and stat to mousein mouse out 
    button.addEventListener('mouseover' , stopSlideShow );
   button.addEventListener('mouseout' , startSlideShow );

  });
};



//forEach sab button ko  alg  alg kar deta hai
buttons.forEach((button,i)=>{
   button.addEventListener('click',()=>{
    resetBg();
    slider.style.transform=`translateX(-${i*800}px)`;
    slideNumber= i + 1;
    button.style.backgroundColor='white'; //har dost  pe click karta hi o white ho ga 2
   });
});
// arrow ka satth move hona vala dots 
const changeColor =()=>{
  resetBg();
  buttons[slideNumber-1].style.backgroundColor='white'
}


//button dots end 
//step2 end



//step1
const nextslde = () => {
  console.log("viks");
  slider.style.transform = `translateX(-${slideNumber * 800}px)`;
  slideNumber++;
  console.log("click");
}

const prevslide = () => {
  console.log("viks");
  slider.style.transform = `translateX(-${(slideNumber-2)*800}px)`;
  slideNumber--;
  console.log("click");
}
//next > pe click karta hi aga janeka liya
const getFirstSlide = () => {
  slider.style.transform = `translateX(0px)`;
  slideNumber = 1;
  console.log("write");
  console.log("break");
}
//aga jata hi  jab slider imge last ma aayto vapis first image peaana ka liya
const getlastSlide = () => {
  slider.style.transform = `translateX(-${(length-1)*800}px)`;
  slideNumber = length;
  console.log("write");
  console.log("break");
}
////let button pe clicked karta hi image ma 1+ hoga tab chala ga

right.addEventListener('click', () => {
  slideNumber < length ? nextslde() : getFirstSlide();
  changeColor();
});
//let button pe clicked karta hi image se -1 hoga tab chala ga
left.addEventListener('click', () => {
  slideNumber > 1 ? prevslide() : getlastSlide();
 changeColor();
});
//step;1 end 

//pura code kouper coppy paste kara ga 
/*
//for button 
//button ko select kiya 
const button=document.querySelector('.bottom');

 //for loop use kara ga jo  i=0 and (i<length sevhota raha ga tab tak run ho ga loop )
for(i=0; i<length; i++){
  //const div  ko eak variablema storekara ga 
   const div=document.createElement('div');
   //div button ko selectkarga
   div.className='button';
   // jo div button ki class hai use ka under save ho ga 
   button.appendChild(div);
};
// sab button ko buttons var ma store kar liya
const  buttons = document.querySelectorAll('.button');
buttons[0].style.backgroundColor = 'white';//style bg in button 
//dot pe click karta hi white se transprant color ho jay
const resetBg=()=>{
  //1
  buttons.forEach((button)=>{
    button.style.backgroundColor='transparent';
  });
};



//forEach sab button ko  alg  alg kar deta hai
buttons.forEach((button,i)=>{
   button.addEventListener('click',()=>{
    resetBg();
    slider.style.transform=`translateX(-${i*800}px)`;
    slideNumber= i + 1;
    button.style.backgroundColor='white'; //har dost  pe click karta hi o white ho ga 2
   });
}); 

*/
//startauto slider

let slideInterval;

const startSlideShow=()=>{
  //setinterval is a in build javascript function to define a time to auto play
  slideInterval=setInterval(()=>{
    slideNumber < length ? nextslde() : getFirstSlide();
    changeColor();

  },2500);
};

const stopSlideShow =()=>{
  clearInterval(slideInterval);
};

startSlideShow();
slider.addEventListener('mouseover' , stopSlideShow );
slider.addEventListener('mouseout' , startSlideShow );

right.addEventListener('mouseover' , stopSlideShow );
right.addEventListener('mouseout' , startSlideShow );

left.addEventListener('mouseover' , stopSlideShow );
left.addEventListener('mouseout' , startSlideShow );

