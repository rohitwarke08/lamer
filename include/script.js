document.addEventListener("DOMContentLoaded", () => {
    const openBtn = document.querySelectorAll(".open-btn");
    const modall = document.querySelector(".modall");
    const form = document.querySelector(".form");
    const formbtn = document.querySelector(".formbtn");
    const modalForm = document.querySelector(".modal-form");
    const modalFormBtn = document.querySelector(".modalFormBtn");
    const closes = document.querySelector(".closeModal");
    const toast = document.querySelector(".toast");
    const fname = form?.querySelector(".fname");
    const lname = form?.querySelector(".lname");
    const email = form?.querySelector(".email");
    const phone = form?.querySelector(".phone");
    const fname1 = modalForm?.querySelector(".fname");
    const lname1 = modalForm?.querySelector(".lname");
    const email1 = modalForm?.querySelector(".email");
    const phone1 = modalForm?.querySelector(".phone");
    const recamenities = document.querySelector(".recamenities");
    const roofamenities = document.querySelector(".roofamenities");
    const recbutton = document.querySelector(".recbutton");
    const roofbutton = document.querySelector(".roofbutton");
    const openBtn1 = document.querySelectorAll(".open-btn1");
    const modalll = document.querySelector(".modalll");
  
    const sheetUrl = "https://script.google.com/macros/s/AKfycbzBWU5MOvYC58ZcttUCIBx4trkbRs2uJk4jKm28P4pBeoecVHiGK3i_TJGEhsgAX99axw/exec";
    
    // Open modal buttons
    openBtn.forEach((item) => {
      item.addEventListener("click", () => {
        modall.classList.add("active");
      });
    });
  
    // Close modal
    closes.addEventListener("click", () => {
      modall.classList.remove("active");
    });
  
    // Name, email, and phone validation patterns
    const namePattern = /^[a-zA-Z\s'-]+$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^\+?\d{8,15}$/;
  
    // On-page load modal activation
    setTimeout(() => {
      modall.classList.add("active");
    }, 6000); // Show modal after 6 seconds
  
    // Form submit logic
    const handleFormSubmit = (e, formElements, data, target, modal) => {
      const { fname, lname, email, phone } = formElements;
  
      if (!fname.value || !lname.value || !email.value || !phone.value) {
        showToast("Please fill up required fields", "red");
      } else if (!namePattern.test(fname.value)) {
        showToast("Please enter a valid First Name", "red");
      } else if (!namePattern.test(lname.value)) {
        showToast("Please enter a valid Last Name", "red");
      } else if (!emailRegex.test(email.value)) {
        showToast("Please enter a valid email address", "red");
      } else if (!phoneRegex.test(phone.value)) {
        showToast("Please enter a valid phone number", "red");
        e.preventDefault();
        return;
      } else {
        target.innerHTML = "Submitting...";
        fetch(sheetUrl, {
          method: "POST",
          body: data,
        })
          .then((res) => res.text())
          .then(() => {
            target.innerHTML = "Submit";
            showToast("Successful!", "#00dd34");
            resetFormFields(formElements);
  
            if (modal) {
              modal.classList.remove("active");
            }
  
            setTimeout(() => {
              window.location.replace("/thankyou.html");
            }, 2000);
          });
      }
      e.preventDefault();
    };
  
    // Toast function
    const showToast = (message, color) => {
      toast.innerHTML = message;
      toast.style.color = color;
      toast.style.top = "10px";
      setTimeout(() => {
        toast.style.top = "-100px";
        toast.style.color = "#000";
      }, 2000);
    };
  
    // Reset form fields
    const resetFormFields = ({ fname, lname, email, phone }) => {
      fname.value = "";
      lname.value = "";
      email.value = "";
      phone.value = "";
    };
  
    // Main form submit
    formbtn?.addEventListener("click", (e) => {
      const data = new FormData(form);
      handleFormSubmit(e, { fname, lname, email, phone }, data, e.target);
    });
  
    // Modal form submit
    modalFormBtn?.addEventListener("click", (e) => {
      const data = new FormData(modalForm);
      handleFormSubmit(e, { fname: fname1, lname: lname1, email: email1, phone: phone1 }, data, e.target, modall);
    });
  
    // Toggle active classes for amenities
    recbutton?.addEventListener("click", () => {
      recamenities.classList.add("active");
      roofamenities.classList.remove("active");
      recbutton.classList.add("active");
      roofbutton.classList.remove("active");
    });
  
    roofbutton?.addEventListener("click", () => {
      roofamenities.classList.add("active");
      recamenities.classList.remove("active");
      roofbutton.classList.add("active");
      recbutton.classList.remove("active");
    });
  });
  