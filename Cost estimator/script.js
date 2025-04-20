let sourceAutocomplete, destinationAutocomplete;

function initAutocomplete() {
  sourceAutocomplete = new google.maps.places.Autocomplete(document.getElementById("source"));
  destinationAutocomplete = new google.maps.places.Autocomplete(document.getElementById("destination"));
  
  // Set minimum date for appointment to today
  const today = new Date().toISOString().split('T')[0];
  document.getElementById("date").min = today;
  
  // Handle form submission
  document.getElementById("appointmentForm").addEventListener("submit", bookAppointment);
}

function calculateFare() {
  const source = document.getElementById("source").value;
  const destination = document.getElementById("destination").value;
  const resultBox = document.getElementById("result");

  if (!source || !destination) {
    resultBox.innerHTML = "<i class='fas fa-exclamation-circle'></i> Please enter both source and destination.";
    return;
  }

  resultBox.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Calculating fare...";

  const service = new google.maps.DistanceMatrixService();

  service.getDistanceMatrix(
    {
      origins: [source],
      destinations: [destination],
      travelMode: "DRIVING",
      unitSystem: google.maps.UnitSystem.METRIC,
    },
    (response, status) => {
      if (status !== "OK") {
        resultBox.innerHTML = "<i class='fas fa-times-circle'></i> Error getting distance.";
        return;
      }

      const element = response.rows[0].elements[0];
      if (element.status !== "OK") {
        resultBox.innerHTML = "<i class='fas fa-route'></i> Route not found.";
        return;
      }

      const distanceKm = element.distance.value / 1000;
      const durationMin = element.duration.value / 60;

      // Fare calculation
      const baseFare = 10;
      const perKm = 11;
      const perMin = 2;

      const price = baseFare + (distanceKm * perKm) + (durationMin * perMin);

      resultBox.innerHTML = 
        `<i class="fas fa-road"></i> Distance: ${element.distance.text}<br>
         <i class="fas fa-clock"></i> Duration: ${element.duration.text}<br><br>
         <strong> Estimated Fare: ₹${price.toFixed(2)}</strong>`;
    }
  );
}

function bookAppointment(e) {
  e.preventDefault();
  
  const resultBox = document.getElementById("appointmentResult");
  resultBox.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Booking your appointment...";
  
  const formData = {
    name: document.getElementById("name").value,
    email: document.getElementById("email").value,
    phone: document.getElementById("phone").value,
    carModel: document.getElementById("carModel").value,
    date: document.getElementById("date").value,
    time: document.getElementById("time").value
  };
  
  fetch('appointment.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(formData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      resultBox.innerHTML = `<i class="fas fa-check-circle"></i> ${data.message}<br><br>
        <strong>Appointment Details:</strong><br>
        Name: ${formData.name}<br>
        Car Model: ${formData.carModel}<br>
        Date: ${formData.date}<br>
        Time: ${formData.time}`;
      
      // Reset form
      document.getElementById("appointmentForm").reset();
    } else {
      resultBox.innerHTML = `<i class="fas fa-times-circle"></i> ${data.message}`;
    }
  })
  .catch(error => {
    resultBox.innerHTML = `<i class="fas fa-times-circle"></i> Error: ${error.message}`;
  });
}