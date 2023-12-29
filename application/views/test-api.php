<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>API Data Table</title>
</head>
<body>
  <table id="dataTable">
    <thead>
      <tr>
        <th>Column 1</th>
        <th>Column 2</th>
        <!-- Add more columns as needed -->
      </tr>
    </thead>
    <tbody id="table_body">
      <!-- Table body will be populated dynamically -->
    </tbody>
  </table>

  <script>
       var myHeaders = new Headers();
myHeaders.append("Content-Type", "application/json");
myHeaders.append("Cookie", "ci_session=ecb024r3hbulnqf72ed2um9qndt23s3g");

var raw = JSON.stringify({
  "endpoint": "update-event-appearance-state",
  "data": {
    "user_id": 44,
    "oneapp_event_id": 1361,
    "appearance_state": "DISPLAYED"
  }
});

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: raw,
  redirect: 'follow'
};

fetch("http://lt-mm-dec.test/event/updateEventAppearanceState", requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));

  </script>
</body>
</html>
