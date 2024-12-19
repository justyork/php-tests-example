# ABTest API Functional Tests

This project contains functional tests for the ABTest API. The tests cover various functionalities of the ABTest API, ensuring that the endpoints work as expected.

## Test Descriptions

### `CreateABTestTest.php`
- **test_create_ab_test**: Tests the creation of a new AB test. It sends a POST request with the necessary data and verifies that the response status is 201 and the response JSON contains the correct data.

### `DeleteABTestTest.php`
- **testDeleteExistingABTest**: Tests the deletion of an existing AB test. It sends a DELETE request and verifies that the response status is 204.
- **testDeleteExistingNonDraftABTest**: Tests the deletion of a non-draft AB test. It sends a DELETE request and verifies that the response status is 417 and the response JSON contains the correct error message.
- **testDeleteNonExistingABTest**: Tests the deletion of a non-existing AB test. It sends a DELETE request and verifies that the response status is 404.

### `FindABTestByIdTest.php`
- **testFindABTestById**: Tests finding an AB test by its ID. It sends a GET request and verifies that the response status is 200 and the response JSON contains the correct data.
- **testFindABTestByName**: Tests finding an AB test by its name. It sends a GET request and verifies that the response status is 200 and the response JSON contains the correct data.
- **testFindNonExistingABTest**: Tests finding a non-existing AB test. It sends a GET request and verifies that the response status is 404.

### `GetAllABTestsTest.php`
- **testGetAllABTestsByAdmin**: Tests retrieving all AB tests by an admin user. It sends a GET request and verifies that the response status is 200 and the response JSON contains the correct number of AB tests.
- **testSearchABTestsByFields**: Tests searching AB tests by various fields. It sends a GET request with search parameters and verifies that the response status is 200 and the response JSON contains the correct data.

### `PublishABTestTest.php`
- **testPublishABTest**: Tests publishing an AB test. It sends a POST request and verifies that the response status is 200 and the database contains the correct log entry.
- **testPublishDraftABTest**: Tests publishing a draft AB test. It sends a POST request and verifies that the response status is 422.
- **testUpdateNonExistingABTest**: Tests publishing a non-existing AB test. It sends a POST request and verifies that the response status is 422.

### `UpdateABTestTest.php`
- **testUpdateExistingABTest**: Tests updating an existing AB test. It sends a PUT request with the necessary data and verifies that the response status is 200 and the response JSON contains the correct data.
- **testUpdateNonExistingABTest**: Tests updating a non-existing AB test. It sends a PUT request and verifies that the response status is 422.
- **testUpdateExistingABTestWithEmptyValues**: Tests updating an existing AB test with empty values. It sends a PUT request and verifies that the response status is 422 and the response JSON contains the correct error messages.

## Running the Tests

To run the tests, use the following command:

```bash
php artisan test
```

Make sure to set up your testing environment and database before running the tests.