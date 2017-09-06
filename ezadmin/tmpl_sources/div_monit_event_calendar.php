<div class="page_title">®event_calendar_title®</div>

<form method="GET" class="pagination" style="width: 100%">
    
    <input type="hidden" name="action" value="<?php echo $input['action']; ?>" >
    <input type="hidden" name="post" value="">
    
    
    <div class="form-group">
        <div class="row">
            <div class="col-md-4 col-md-offset-3">
                <label class="sr-only" for="classroom">®monit_classroom®</label>
                <input type="text" class="form-control classroomSuggest" name="classroom" id="context" placeholder="®monit_classroom®"
                    value="<?php if (isset($input) && array_key_exists('classroom', $input)) {
    echo $input['classroom'];
} ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-block btn-success">
                    <span class="glyphicon glyphicon-search icon-white"></span> 
                    ®search®
                </button>
            </div>
        </div>
    </div>
</form>


<?php if ($dateEvent == "[]") {
    ?>
    <div class="col-md-offset-2 col-md-8 alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        ®calendar_no_result®
    </div>
    <br />
<?php
} ?>

<div ng-app="mwl.calendar.docs">
    <div ng-controller="KitchenSinkCtrl as vm">
      <h2 class="col-md-12 text-center">{{ vm.calendarTitle }}</h2>

      <div class="row">

        <div class="col-md-6 text-center">
          <div class="btn-group">

            <button
              class="btn btn-primary"
              mwl-date-modifier
              date="vm.viewDate"
              decrement="vm.calendarView">
              ®previous®
            </button>
            <button
              class="btn btn-default"
              mwl-date-modifier
              date="vm.viewDate"
              set-to-today>
              ®today®
            </button>
            <button
              class="btn btn-primary"
              mwl-date-modifier
              date="vm.viewDate"
              increment="vm.calendarView">
              ®continue®
            </button>
          </div>
        </div>

        <br class="visible-xs visible-sm">

        <div class="col-md-6 text-center">
          <div class="btn-group">
            <label class="btn btn-primary" ng-model="vm.calendarView" uib-btn-radio="'year'">®calendar_year®</label>
            <label class="btn btn-primary" ng-model="vm.calendarView" uib-btn-radio="'month'">®calendar_month®</label>
            <label class="btn btn-primary" ng-model="vm.calendarView" uib-btn-radio="'week'">®calendar_week®</label>
          </div>
        </div>

      </div>

      <br>

      <mwl-calendar
        events="vm.events"
        view="vm.calendarView"
        view-title="vm.calendarTitle"
        view-date="vm.viewDate"
        on-event-click="vm.eventClicked(calendarEvent)"
        on-event-times-changed="calendarEvent.startsAt = calendarNewEventStart; calendarEvent.endsAt = calendarNewEventEnd"
        cell-is-open="vm.isCellOpen"
        day-view-start="06:00"
        day-view-end="22:59"
        day-view-split="30"
        on-view-change-click="vm.viewChangeClicked(calendarNextView)">
      </mwl-calendar>

      <br><br><br>


    </div>
</div>
    
<script>
    moment.locale('FR_fr', {
        week : {
          dow : 1 // Monday is the first day of the week
        },
        weekdays : ["®calendar_sunday®", 
            "®calendar_monday®", 
            "®calendar_tuesday®", 
            "®calendar_wednesday®", 
            "®calendar_thursday®", 
            "®calendar_friday®",
            "®calendar_saturday®"],
        months: [
            "®calendar_january®",
            "®calendar_february®",
            "®calendar_march®",
            "®calendar_april®",
            "®calendar_may®",
            "®calendar_june®",
            "®calendar_july®",
            "®calendar_august®",
            "®calendar_september®",
            "®calendar_october®",
            "®calendar_november®",
            "®calendar_december®"
        ]
        
    });
    moment.locale('FR_fr');
    
    angular.module('mwl.calendar.docs', ['mwl.calendar', 'ui.bootstrap']);
    
    angular
        .module('mwl.calendar.docs')
        .controller('KitchenSinkCtrl', function(moment, calendarConfig) {

    var vm = this;

    //These variables MUST be set as a minimum for the calendar to work
    vm.calendarView = 'month';
    vm.viewDate = new Date();
    calendarConfig.dateFormatter = 'moment';
    
    vm.events = <?php echo $dateEvent; ?>
    
    vm.viewChangeClicked = function(nextView) {
            if (nextView === 'day') {
                return false;
            }
        };
    
    vm.eventClicked = function(event) {
        document.location.href="index.php?action=view_track_asset&post=&startDate=0&asset="+event.asset+"&view_all=on";
        };
        

    vm.isCellOpen = false;

  })
  .factory('alert', function($uibModal) {

    function show(action, event) {
      return $uibModal.open({
        templateUrl: 'modalContent.html',
        controller: function() {
          var vm = this;
          vm.action = action;
          vm.event = event;
        },
        controllerAs: 'vm'
      });
    }

    return {
      show: show
    };

  });

$('.classroomSuggest').typeahead({
  hint: true,
  highlight: true,
  minLength: 0
},
{
  name: 'classroom',
  source: substringMatcher(<?php echo $js_classroom; ?>)
});
</script>