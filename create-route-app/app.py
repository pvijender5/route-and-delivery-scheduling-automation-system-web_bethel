import os
from flask import Flask, request, jsonify
from flask_cors import CORS
from gevent.pywsgi import WSGIServer
# [START import]
from functools import partial
from ortools.constraint_solver import routing_enums_pb2
from ortools.constraint_solver import pywrapcp
import requests
import json
import urllib
import urllib.request as ur
import math
import logging
# [END import]

app = Flask(__name__)
CORS(app)
# [START data_model]
def create_data_model(requesData):

    """Stores the data for the problem."""
    data = {}
    #Distance matrix
    # data['addresses'] = ['3610+Hacks+Cross+Rd+Memphis+TN', # depot
    #                  '1921+Elvis+Presley+Blvd+Memphis+TN',
    #                  '149+Union+Avenue+Memphis+TN',
    #                  '1034+Audubon+Drive+Memphis+TN',
    #                  '1532+Madison+Ave+Memphis+TN',
    #                  '706+Union+Ave+Memphis+TN',
    #                  '3641+Central+Ave+Memphis+TN',
    #                  '926+E+McLemore+Ave+Memphis+TN',
    #                  '4339+Park+Ave+Memphis+TN',
    #                  '600+Goodwyn+St+Memphis+TN',
    #                  '2000+North+Pkwy+Memphis+TN',
    #                  '262+Danny+Thomas+Pl+Memphis+TN',
    #                  '125+N+Front+St+Memphis+TN',
    #                  '5959+Park+Ave+Memphis+TN',
    #                  '814+Scott+St+Memphis+TN',
    #                  '1005+Tillman+St+Memphis+TN'
    #                 ]

    data['addresses']=requesData['address']

    data['API_key']="AI&hjg%DF34hgTRJMBDJHDbg^Frrt^R"
    distance_matrix = create_distance_matrix(data) 
    
    #distance_matrix=""
    
    if distance_matrix is None:
         return {"success":bool(0), "message": "Unable to generate the distance matrix. Please check the provided addresses or verify the rate limit of APIs."}
         
    # if error return 
    if 'success' in distance_matrix and distance_matrix['success'] == False:
         return distance_matrix
    
    #data["distance_matrix"] = [[0, 26922, 9609, 6519, 8639], [26661, 0, 33386, 31994, 34829], [9620, 33930, 0, 4082, 952], [6542, 33224, 4092, 0, 5132], [8636, 35282, 915, 4940, 0]]
    
    distance_matrix[0] = [0] * len(distance_matrix[0])
    data["distance_matrix"] = distance_matrix
    data['num_locations'] = len(data['distance_matrix'])
    # data['time_windows'] = [
    #     (0, 1440),  # depot
    #     (0, 1440),  # 1
    #     (0, 1440),  # 2
    #     (0, 1440),  # 3
    #     (0, 1440),  # 4
    #     (0, 1440),  # 5
    #     (0, 1440),  # 6
    #     (0, 1440),  # 7
    #     (0, 1440),  # 8
    #     (0, 1440),  # 9
    #     (0, 1440),  # 10
    #     (0, 1440),  # 11
    #     (0, 1440),  # 12
    #     (0, 1440),  # 13
    #     (0, 1440),  # 14
    #     (0, 1440),  # 15
    #     (0, 1440),  # 16
    # ]
    # data['time_windows'] = [
    #        (0, 500),
    #        (0, 500), 
    #        (0, 1440),
    # ]
    data['time_windows']=requesData['time_windows']
  
    #data['demands'] =  [0, 4, 4] 
    #data['demands'] =  [0, 1, 1, 2, 4, 2, 4, 8, 8, 1, 2, 1, 2, 4, 4, 8, 8]
    data['demands'] = requesData['demands']
    #data['time_per_demand_unit'] = 5  # 5 minutes/unit
    data['time_per_demand_unit'] = 0  # 5 minutes/unit
    #data['num_vehicles'] = len(requesData['demands'])
    #data['num_vehicles'] = int(len(requesData['demands']) / 2)
    #if len(requesData['demands']) > 3:
    # data['num_vehicles'] =  math.ceil(len(requesData['demands']) / 4)
    #else:
    # data['num_vehicles'] = 1
    #vehicle_capacity=6000
    data['num_vehicles'] =  len(requesData['demands'])
    print('num_vehicles',data['num_vehicles'])

    vehicle_capacity=requesData['vehicle_capacity']

    #data['vehicle_capacities'] = [6000, 6000, 6000, 6000]
    data['vehicle_capacities'] = [vehicle_capacity] * data['num_vehicles']


    #data['vehicle_capacities'] = [8, 8, 8, 8]
    #data['vehicle_speed'] = 83  # Travel speed: 5km/h converted in m/min
    #data['vehicle_speed'] = 1167  # Travel speed: 40km/h converted in m/min
    data['vehicle_speed'] = 1215   # Travel speed: 70km/h converted in m/min
    data['depot'] = 0
    return data
    # [END data_model]


#crete distance matrix by using google apis
def create_distance_matrix(data):
    addresses = data["addresses"]
    API_key = data["API_key"]
    # Distance Matrix API only accepts 100 elements per request, so get rows in multiple requests.
    max_elements = 100
    num_addresses = len(addresses) # 16 in this example.
    # Maximum number of rows that can be computed per request (6 in this example).
    max_rows = max_elements // num_addresses
    # num_addresses = q * max_rows + r (q = 2 and r = 4 in this example).
    q, r = divmod(num_addresses, max_rows)
    dest_addresses = addresses
    distance_matrix = []
    # Send q requests, returning max_rows rows per request.
    for i in range(q):
        origin_addresses = addresses[i * max_rows: (i + 1) * max_rows]
        response = send_request(origin_addresses, dest_addresses, API_key)

        # Check if any address is not found
        not_found = False
        

        for row in response['rows']:
            for element in row['elements']:
                if element['status'] == 'NOT_FOUND':
                    not_found = True
                    break  # Exit the inner loop if a 'NOT_FOUND' status is found
            if not_found:
                break  # Exit the outer loop if a 'NOT_FOUND' status is found
        if not_found:
            return {"success":bool(0), "message": "Address not found, Please check the provided addresses."}
        else:
            distance_matrix += build_distance_matrix(response)
            
            
        
        

    # Get the remaining remaining r rows, if necessary.
    if r > 0:
        origin_addresses = addresses[q * max_rows: q * max_rows + r]
        response = send_request(origin_addresses, dest_addresses, API_key)
        # Check if any address is not found
        not_found = False

        

        for row in response['rows']:
            for element in row['elements']:
                if element['status'] == 'NOT_FOUND':
                    not_found = True
                    break  # Exit the inner loop if a 'NOT_FOUND' status is found
            if not_found:
                break  # Exit the outer loop if a 'NOT_FOUND' status is found
        if not_found:
            return {"success":bool(0), "message": "Address not found, Please check the provided addresses."}
        else:
            distance_matrix += build_distance_matrix(response)
        

    
     
    print("distance_matrix",distance_matrix)          
    return distance_matrix  
#   return [
#         # fmt: off
#       [0, 548, 776, 696, 582, 274, 502, 194, 308, 194, 536, 502, 388, 354, 468, 776, 662],
#       [548, 0, 684, 308, 194, 502, 730, 354, 696, 742, 1084, 594, 480, 674, 1016, 868, 1210],
#       [776, 684, 0, 992, 878, 502, 274, 810, 468, 742, 400, 1278, 1164, 1130, 788, 1552, 754],
#       [696, 308, 992, 0, 114, 650, 878, 502, 844, 890, 1232, 514, 628, 822, 1164, 560, 1358],
#       [582, 194, 878, 114, 0, 536, 764, 388, 730, 776, 1118, 400, 514, 708, 1050, 674, 1244],
#       [274, 502, 502, 650, 536, 0, 228, 308, 194, 240, 582, 776, 662, 628, 514, 1050, 708],
#       [502, 730, 274, 878, 764, 228, 0, 536, 194, 468, 354, 1004, 890, 856, 514, 1278, 480],
#       [194, 354, 810, 502, 388, 308, 536, 0, 342, 388, 730, 468, 354, 320, 662, 742, 856],
#       [308, 696, 468, 844, 730, 194, 194, 342, 0, 274, 388, 810, 696, 662, 320, 1084, 514],
#       [194, 742, 742, 890, 776, 240, 468, 388, 274, 0, 342, 536, 422, 388, 274, 810, 468],
#       [536, 1084, 400, 1232, 1118, 582, 354, 730, 388, 342, 0, 878, 764, 730, 388, 1152, 354],
#       [502, 594, 1278, 514, 400, 776, 1004, 468, 810, 536, 878, 0, 114, 308, 650, 274, 844],
#       [388, 480, 1164, 628, 514, 662, 890, 354, 696, 422, 764, 114, 0, 194, 536, 388, 730],
#       [354, 674, 1130, 822, 708, 628, 856, 320, 662, 388, 730, 308, 194, 0, 342, 422, 536],
#       [468, 1016, 788, 1164, 1050, 514, 514, 662, 320, 274, 388, 650, 536, 342, 0, 764, 194],
#       [776, 868, 1552, 560, 674, 1050, 1278, 742, 1084, 810, 1152, 274, 388, 422, 764, 0, 798]
#         # fmt: on
#     ]

#Send api request to google apis 
def send_request(origin_addresses: list, dest_addresses: list, api_key: str) -> dict:
    """Build and send request to Google Maps Distance Matrix API.
    
    Args:
        origin_addresses: List of origin addresses.
        dest_addresses: List of destination addresses.
        api_key: Google Maps API key.
    
    Returns:
        dict: JSON response from the API containing distance matrix data.
    """
    def build_address_str(addresses: list) -> str:
        """Build a pipe-separated string of addresses."""
        return '|'.join(addresses)

    base_url = 'https://maps.googleapis.com/maps/api/distancematrix/json'
    params = {
        'units': 'imperial',
        'origins': build_address_str(origin_addresses),
        'destinations': build_address_str(dest_addresses),
        'key': api_key
    }
    
    request_url = f"{base_url}?{'&'.join(f'{k}={v}' for k, v in params.items())}"
    
    try:
        with ur.urlopen(request_url) as response:
            return json.loads(response.read())
    except ur.URLError as e:
        print(f"Error fetching distance matrix: {e}")
        raise
    except json.JSONDecodeError as e:
        print(f"Error parsing API response: {e}")
        raise

def build_distance_matrix(response: dict) -> list:
    """Build distance matrix from API response.
    
    Args:
        response: JSON response from Google Maps Distance Matrix API.
    
    Returns:
        list: 2D list containing distances between points.
    """
    return [[element['distance']['value'] 
             for element in row['elements']] 
            for row in response['rows']]



#######################
# Problem Constraints #
#######################
def create_distance_evaluator(data):
    """Creates callback to return distance between points."""
    def distance_evaluator(manager, from_node, to_node):
        """Returns the distance between the two nodes"""
        return data['distance_matrix'][manager.IndexToNode(from_node)][manager.IndexToNode(to_node)]

    return distance_evaluator


def create_demand_evaluator(data):
    """Creates callback to get demands at each location."""
    _demands = data['demands']

    def demand_evaluator(manager, node):
        """Returns the demand of the current node"""
        return _demands[manager.IndexToNode(node)]

    return demand_evaluator


def add_capacity_constraints(routing, data, demand_evaluator_index):
    """Adds capacity constraint"""
    capacity = 'Capacity'
    routing.AddDimensionWithVehicleCapacity(
        demand_evaluator_index,
        0,  # null capacity slack
        data['vehicle_capacities'],
        True,  # start cumul to zero
        capacity)


def create_time_evaluator(data):
    """Creates callback to get total times between locations."""

    def service_time(data, node):
        """Gets the service time for the specified location."""
        return data['demands'][node] * data['time_per_demand_unit']

    def travel_time(data, from_node, to_node):
        """Gets the travel times between two locations."""
        if from_node == to_node:
            travel_time = 0
        else:
            travel_time = data['distance_matrix'][from_node][to_node] / data['vehicle_speed']
        return travel_time

    _total_time = {}
    # precompute total time to have time callback in O(1)
    for from_node in range(data['num_locations']):
        _total_time[from_node] = {}
        for to_node in range(data['num_locations']):
            if from_node == to_node:
                _total_time[from_node][to_node] = 0
            else:
                _total_time[from_node][to_node] = int(
                    service_time(data, from_node) + travel_time(
                        data, from_node, to_node))

    def time_evaluator(manager, from_node, to_node):
        """Returns the total time between the two nodes"""
        return _total_time[manager.IndexToNode(from_node)][manager.IndexToNode(to_node)]

    return time_evaluator


def add_time_window_constraints(routing, manager, data, time_evaluator_index):
    """Add Global Span constraint"""
    time = 'Time'
    horizon = 120 * 60  # Max time window duration in minutes
    horizon = 120
    routing.AddDimension(
        time_evaluator_index,
        1440,  # allow waiting time
        1440,  # maximum time per vehicle
        False,  # don't force start cumul to zero since we are giving TW to start nodes
        time)
    time_dimension = routing.GetDimensionOrDie(time)
    # Add time window constraints for each location except depot
    # and 'copy' the slack var in the solution object (aka Assignment) to print it
    for location_idx, time_window in enumerate(data['time_windows']):
        if location_idx == 0:
            continue
        index = manager.NodeToIndex(location_idx)
        time_dimension.CumulVar(index).SetRange(time_window[0], time_window[1])
        routing.AddToAssignment(time_dimension.SlackVar(index))
    # Add time window constraints for each vehicle start node
    # and 'copy' the slack var in the solution object (aka Assignment) to print it
    for vehicle_id in range(data['num_vehicles']):
        index = routing.Start(vehicle_id)
        time_dimension.CumulVar(index).SetRange(data['time_windows'][0][0],
                                                data['time_windows'][0][1])
        routing.AddToAssignment(time_dimension.SlackVar(index))
        # Warning: Slack var is not defined for vehicle's end node
        #routing.AddToAssignment(time_dimension.SlackVar(self.routing.End(vehicle_id)))

# [START solution_printer]
def print_solution(manager, routing, assignment):  # pylint:disable=too-many-locals
    """Prints assignment on console"""
    route = {}
    route_elements_array = []
    print(f'Objective: {assignment.ObjectiveValue()}')
    time_dimension = routing.GetDimensionOrDie('Time')
    capacity_dimension = routing.GetDimensionOrDie('Capacity')
    total_distance = 0
    total_load = 0
    total_time = 0
    requesData = request.json
    demands=requesData['demands']

    for vehicle_id in range(manager.GetNumberOfVehicles()):
        route_element = []
        time_element = []
        load_element = []
        distance_element = [0]
        elements = {}
        index = routing.Start(vehicle_id)
        plan_output = 'Route for vehicle {}:\n'.format(vehicle_id)
        distance = 0
        while not routing.IsEnd(index):
            load_var = capacity_dimension.CumulVar(index)
            time_var = time_dimension.CumulVar(index)
            slack_var = time_dimension.SlackVar(index)

            # append route in array   
            route_element.append(manager.IndexToNode(index))
            # append time in array         
            time_element.append(assignment.Value(time_var))

            # append load in array         
            load_element.append(demands[manager.IndexToNode(index)])

            
            
            

            plan_output += ' {0} Load({1}) Time({2},{3}) Slack({4},{5}) ->'.format(
                manager.IndexToNode(index),
                assignment.Value(load_var),
                assignment.Min(time_var),
                assignment.Max(time_var),
                assignment.Min(slack_var), assignment.Max(slack_var))
            previous_index = index
            index = assignment.Value(routing.NextVar(index))
            distance += routing.GetArcCostForVehicle(previous_index, index,vehicle_id)
            # append distance in array         
            distance_element.append(routing.GetArcCostForVehicle(previous_index, index,vehicle_id))

        load_var = capacity_dimension.CumulVar(index)
        time_var = time_dimension.CumulVar(index)
        slack_var = time_dimension.SlackVar(index)
        # append route in array   
        route_element.append(manager.IndexToNode(index))
        # append time in array  
        time_element.append(assignment.Value(time_var))
        
        # append load in array         
        load_element.append(demands[manager.IndexToNode(index)])

      

        # append distance in array         
        #distance_element.append(distance)

        plan_output += ' {0} Load({1}) Time({2},{3})\n'.format(
            manager.IndexToNode(index),
            assignment.Value(load_var),
            assignment.Min(time_var), assignment.Max(time_var))
        plan_output += 'Distance of the route: {0}m\n'.format(distance)
        plan_output += 'Load of the route: {}\n'.format(
            assignment.Value(load_var))
        plan_output += 'Time of the route: {}\n'.format(
            assignment.Value(time_var))
        print(plan_output)
        total_distance += distance
        total_load += assignment.Value(load_var)
        total_time += assignment.Value(time_var)

        

        #append all record in array
        elements['totalLoad']=assignment.Value(load_var)
        elements['route']=route_element
        elements['time']=time_element
        elements['distance']=distance_element
        elements['load']=load_element
        elements['totalDistance']=distance
        elements['totalTime']=assignment.Value(time_var)
        route_elements_array.append(elements)
    print('Total Distance of all routes: {0}m'.format(total_distance))
    print('Total Load of all routes: {}'.format(total_load))
    print('Total Time of all routes: {0}min'.format(total_time))

    route['elements']=route_elements_array
    route['address']=requesData['address']
    
    # route['totalDistance']=total_distance
    # route['totalLoad']=total_load
    # route['totalTime']=total_time

    return route
    # [END solution_printer]


# Endpoint to solve CVRPTW and get the solution
@app.route('/solve_cvrptw', methods=['POST'])
def solve_cvrptw():


    #print("post data", request.data)

    # Get the JSON data from the request
    data = request.json

    # Check if the "address" key exists in the JSON data
    if 'address' not in data:
        return jsonify({"error": "Missing 'address' key in JSON data"}), 400 

    if 'demands' not in data:
        return jsonify({"error": "Missing 'demands' key in JSON data"}), 400
   
    if 'time_windows' not in data:
        return jsonify({"error": "Missing 'time_windows' key in JSON data"}), 400 

    if 'vehicle_capacity' not in data:
        return jsonify({"error": "Missing 'vehicle_capacity' key in JSON data"}), 400    


    """Solve the Capacitated VRP with time windows."""
    # Instantiate the data problem.
    # [START data]
    data = create_data_model(data)
    # [END data]

    # Return  error if scusucess is false
    if 'success'  in data and data['success'] == False:
        return jsonify(data)

    # Create the routing index manager.
    # [START index_manager]

    manager = pywrapcp.RoutingIndexManager(data['num_locations'],
                                           data['num_vehicles'], data['depot'])
    # [END index_manager]

    # Create Routing Model.
    # [START routing_model]
    routing = pywrapcp.RoutingModel(manager)
    # [END routing_model]

    # Define weight of each edge.
    # [START transit_callback]
    distance_evaluator_index = routing.RegisterTransitCallback(
        partial(create_distance_evaluator(data), manager))
    # [END transit_callback]

    # Define cost of each arc.
    # [START arc_cost]
    routing.SetArcCostEvaluatorOfAllVehicles(distance_evaluator_index)
    # [END arc_cost]

    # Add Capacity constraint.
    # [START capacity_constraint]
    demand_evaluator_index = routing.RegisterUnaryTransitCallback(
        partial(create_demand_evaluator(data), manager))
    add_capacity_constraints(routing, data, demand_evaluator_index)
    # [END capacity_constraint]

    # Add Time Window constraint.
    # [START time_constraint]
    time_evaluator_index = routing.RegisterTransitCallback(
        partial(create_time_evaluator(data), manager))
    add_time_window_constraints(routing, manager, data, time_evaluator_index)
    # [END time_constraint]

    # Setting first solution heuristic (cheapest addition).
    # [START parameters]
    search_parameters = pywrapcp.DefaultRoutingSearchParameters()
    search_parameters.first_solution_strategy = (
        routing_enums_pb2.FirstSolutionStrategy.PATH_CHEAPEST_ARC)
    search_parameters.local_search_metaheuristic = (
        routing_enums_pb2.LocalSearchMetaheuristic.GUIDED_LOCAL_SEARCH)
    search_parameters.time_limit.FromSeconds(2)
    search_parameters.log_search = True
    # [END parameters]

    # Solve the problem.
    # [START solve]
    solution = routing.SolveWithParameters(search_parameters)
    # [END solve]

    # Print solution on console.Good morning
    # [START print_solution]
    # if solution:
    #     print_solution(manager, routing, solution)
    # else:
    #     print('No solution found!')
    # [END print_solution]

    if solution:
        result = print_solution(manager, routing, solution)
        return jsonify({"success":bool(1), "message": "solution found", "data": result})
    else:
        return jsonify({"success":bool(0), "message": "No solution found."})


# if __name__ == "__main__":
#    app.run(port=5000, debug=True, host='0.0.0.0')

#if __name__ == '__main__':
#    app.run(host='0.0.0.0', port=5001)

#if __name__ == '__main__':
#      app.run(debug=True)
#http_server = WSGIServer(("127.0.0.1", 5000), app)
#http_server.serve_forever()

