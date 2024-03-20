const steps = document.querySelectorAll('.step');
let currentStep = 1;

function showStep(stepNumber) {
  const step = steps[stepNumber - 1];
  step.classList.add('active');
}

function showNextStep() {
  if (currentStep <= steps.length) {
    showStep(currentStep);
    currentStep++;
    setTimeout(showNextStep, 1000); 
  }
}

showNextStep();
