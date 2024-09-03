# Tests

Each scenario must be tested against the API and the UI.

## Scenario 1: create a spot

1. Access localhost:3000/spots/create
2. Fill the form with the following data:
   - Name: "Spot 1"
   - Description: "Description of spot 1"
   - Latitude: 10
   - Longitude: 20
   - Website target: "https://spotin.ch"
   - Public: Yes
3. Click on the "Save" button
4. The spot is created and the user is redirected to the spot's page.

## Scenario 2: edit a spot

1. Access localhost:3000/spots/{id}/edit
2. Fill the form with the following data:
   - Name: "Spot 1 edited"
3. Click on the "Save" button
4. The spot is updated and the user is redirected to the spot's page.

## Scenario 3: view a spot

1. Access localhost:3000/spots/{id}
2. The spot's page is displayed with the spot's details.

## Scenario 4: download a spot's QR code

1. Access localhost:3000/spots/{id}
2. Click on the "Download QR code" button
3. The spot's QR code is downloaded.

## Scenario 5: access a spot's redirection page

1. Access localhost:3000/spots/{id}
2. Click on the "View redirection page" button
3. The spot's redirection page is displayed with:
   - The spot's name
   - The spot's certification
   - The spot's description
   - The spot's map
   - The spot's website target
   - A button to access the website target
   - A menu to report, like or share the spot

## Scenario 6: Access a spot's website target

1. Access localhost:3000/spots/{id}
2. Click on the "View redirection page" button
3. Click on the "Access https://spotin.ch" button
4. The user is redirected to the website target.

## Scenario 7: delete a spot

1. Access localhost:3000/spots/
2. Click on the "Delete" button of the spot to delete
3. The spot is deleted and the user is redirected to the spots' list.

## Scenario 8: access another user's spot as a guest

1. Access localhost:3000/spots/create
2. Fill the form with the following data:
   - Name: "Spot 2"
   - Description: "Description of spot 2"
   - Latitude: 10
   - Longitude: 20
   - Website target: "https://spotin.ch"
   - Public: Yes
3. Click on the "Save" button
4. The spot is created and the user is redirected to the spot's page.
5. Open a incognito window
6. Access localhost:3000/spots/{id}
7. The spot's page is not displayed and the user is redirected to login page.

## Scenario 8: access another user's spot as an authenticated user

1. Access localhost:3000/spots/create
2. Fill the form with the following data:
   - Name: "Spot 2"
   - Description: "Description of spot 2"
   - Latitude: 10
   - Longitude: 20
   - Website target: "https://spotin.ch"
   - Public: Yes
3. Click on the "Save" button
4. The spot is created and the user is redirected to the spot's page.
5. Open a incognito window
6. Access localhost:3000 and login with another user's credentials
7. Access localhost:3000/spots/{id}
8. The spot's page is not displayed and the user is redirected to the spots' list.

## Scenario 9: a user tries to access a spot's edition page of another user

1. Access localhost:3000/spots/create
2. Fill the form with the following data:
   - Name: "Spot 3"
   - Description: "Description of spot 3"
   - Latitude: 10
   - Longitude: 20
   - Website target: "https://spotin.ch"
   - Public: Yes
3. Click on the "Save" button
4. The spot is created and the user is redirected to the spot's page.
5. Click on edit spot
6. Open a incognito window
7. Access localhost:3000 and login with another user's credentials
8. Access localhost:3000/spots/{id}/edit
9. The spot's edition page is not displayed and the user is redirected to the spots' list.

## Scenario 10: an unconfigured spot is created

1. Access localhost:3000/spots/create
2. Fill the form with the following data:
   - Name: "Spot 4"
   - Description: "Description of spot 4"
   - Latitude: 10
   - Longitude: 20
   - Website target: "https://spotin.ch"
   - Public: No
   - Configuration: No
3. Click on the "Save" button

## Scenario 11: an unconfigured spot is configured

1. Access localhost:3000/spots/{id}/edit
2. Fill the form with the following data:
   - Configuration: Yes
3. Click on the "Save" button
