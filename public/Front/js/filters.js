window.onload=()=>
{
    const FiltersForm=document.querySelector("filters");
    document.querySelectorAll("filters input").forEach(input =>{
        input.addEventListener("change",()=>{
          console.log("clic");
        });
    });
}