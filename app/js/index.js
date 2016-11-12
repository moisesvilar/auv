var app = angular.module('App', []);

app.service('SurveyService', function($http) {
    this.getSurveys = function() {
        return $http.get('http://localhost:8383/listaSurveys.php');
    };
    
    this.updateStatus = function(survey, status) {
        var data = {
            survey_id: survey.survey_id,
            status_id: status.id
        };
        return $http.post('http://localhost:8383/actualizaSurveyStatus.php', data);
    };
});

app.service('AuvService', function($http) {
    this.getAuvs = function() {
        return $http.get('http://localhost:8383/listaAUVs.php');
    };
    
    this.updateVehicle = function(survey, vehicle) {
        var data = {
            survey_id: survey.survey_id,
            vehicle_id: vehicle
        };
        return $http.post('http://localhost:8383/actualizaSurveyVehiculo.php', data);
    };
    
    this.getDevices = function(id) {
        return $http.get('http://localhost:8383/listaAUVDevices.php?id=' + id);
    };
});

app.controller('Controller', function($scope, SurveyService, AuvService) {
    $scope.statusError = null;
    $scope.vehicleError = null;
    this.statusChanged = false;
    this.vehicleChanged = false;
    $scope.selectedAuv = null;
    $scope.statuses = [
        {id: '1', name: 'Inactivo'},
        {id: '2', name: 'Activo'},
        {id: '3', name: 'Pausada'},
        {id: '4', name: 'Finalizada'}
    ];
    $scope.devices = [];

    $scope.surveys = null;
    SurveyService.getSurveys().then(function(data) {
        $scope.surveys = data.data.surveys;
    });

    $scope.auvs = null;
    AuvService.getAuvs().then(function(data) {
        $scope.auvs = data.data.vehicles;
    });

    $scope.isVehicleError = function(survey, index, vehicleStr, error) {
        if (!vehicleStr) {
            return false;
        }
        var vehicle = JSON.parse(vehicleStr);
        if (error) {
            $scope.vehicleError = error;
            return true;
        }
        $scope.vehicleError = null;
        if (survey.survey_id !== $scope.surveys[index].survey_id) {
            return false;
        }
        if (!vehicle) {
            return false;
        }
        if (survey.vehicle_name === vehicle.name) {
            return false;
        }
        return false;
    };
    
    $scope.isStatusError =  function(survey, index, statusStr, error) {
        if (!statusStr) {
            return false;
        }
        var status = JSON.parse(statusStr);
        if (error) {
            $scope.statusError = error;
            return true;
        }
        $scope.statusError = null;
        if (survey.survey_id !== $scope.surveys[index].survey_id) {
            return false;
        }
        if (!status) {
            return false;
        }
        if (survey.status === status.name) {
            return false;
        }
        if (survey.status === 'Inactivo' && status.name !== 'Activo') {
            $scope.statusError = 'De estado inactivo sólo puedes pasar a estado activo.';
            return true;
        }
        if (survey.status === 'Activo' && (status.name !== 'Pausada' && status.name !== 'Finalizada')) {
            $scope.statusError = 'De estado activo sólo puedes pasar a estado pausada o finalizada.';
            return true;
        }
        if (survey.status === 'Pausada' && (status.name !== 'Activo') && status.name !== 'Finalizada') {
            $scope.statusError = 'De estado pausada sólo puedes pasar a estado activo o finalizada.';
            return true;
        }
        if (survey.status === 'Finalizada' && status.name !== 'Finalizada') {
            $scope.statusError = 'De estado finalizada sólo puedes pasar a estado finalizada.';
            return true;
        }
        if (status.name === 'Activo' && !survey.vehicle_id) {
            $scope.statusError = 'No puedes activar una misión sin vehículo asociado';
            return true;
        }
        return false;
    };

    $scope.selectStatus = function(survey, status, index) {
        console.log('Estado de %s cambia de %s a %s', survey.survey_name, survey.status, status);
        if (!this.isStatusError(survey, index, status)) {
            this.statusChanged = true;
        }
        else {
            return;
        }
    };

    $scope.selectVehicle = function(survey, vehicle, index) {
        console.log('Vehiculo de %s cambia de %s a %s', survey.survey_name, survey.vehicle_id, vehicle);
        if (!this.isVehicleError(survey, index, vehicle)) {
            this.vehicleChanged = true;
        }
        else {
            return;
        }
    };
    
    $scope.saveStatus = function(survey, statusStr) {
        var self = this;
        var status = JSON.parse(statusStr);
        SurveyService.updateStatus(survey, status)
        .success(function(data) {
            console.log(data);
            self.statusChanged = false;
        }).error(function(error) {
            $scope.isStatusError(null, null, null, error);
        }); 
    };
    
    $scope.saveVehicle = function(survey, vehicleStr) {
        var self = this;
        var vehicle = JSON.parse(vehicleStr);
        AuvService.updateVehicle(survey, vehicle)
        .success(function(data){
            console.log(data);
            self.vehicleChanged = false;
        }).error(function(error){
            $scope.isVehicleError(null, null, null, error);
        });
    };
    
    $scope.showDevices = function(id) {
        $scope.selectedAuv = id;
        $scope.isShowDevices = false;
        AuvService.getDevices(id)
        .success(function(data){
            $scope.isShowDevices = true;
            $scope.devices = data.devices;
        }).error(function(error) {
            console.log(error);
        });
    };
});