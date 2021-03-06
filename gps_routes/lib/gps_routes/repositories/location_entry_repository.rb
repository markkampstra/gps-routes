require 'haversine'

class LocationEntryRepository < Hanami::Repository
  def all_from_trip
    location_entries.where('trip = \'nordkapp-2019\' and created_at > ?', Hanami::Utils::Kernel.DateTime('2019-07-07')).order{ created_at.desc }
  end

  def aggregate
    entries = location_entries
      .where('trip = \'nordkapp-2019\'')
      .where {
      measured_at > Hanami::Utils::Kernel.DateTime('2019-07-07')
      # trip == 'nordkapp-2019'
    }.order {
      created_at.asc
    }
    total_distance = 0
    last_location = nil
    aggregated_locations = []
    nr_points = 0
    current_entry = nil
    entries.each do |entry|
      current_entry = entry
      last_location = entry if last_location.nil?
      if last_location != entry
        distance = distance_between_locations_in_m(last_location, entry)
        duration = entry.created_at - last_location.created_at
        raw_data = JSON.parse(entry.raw)
        accuracy = raw_data['acc']
        next if accuracy > 50 || (distance > 2000 && duration < 100)
        if distance > 1000
          total_distance += (distance / 1000.0)
          aggregated_locations << {
            lat: last_location.lat,
            lon: last_location.lon,
            nr_points: nr_points,
            time: duration,
            start_at: last_location.created_at,
            end_at: entry.created_at,
            total_distance: total_distance,
            distance_to_previous: distance,
            acc: accuracy
          }
          nr_points = 0
          last_location = entry
        else
          nr_points += 1
        end
      end
    end
    if last_location != current_entry
      distance = distance_between_locations_in_m(last_location, current_entry)
      duration = current_entry.created_at - last_location.created_at
      aggregated_locations << {
        lat: current_entry.lat,
        lon: current_entry.lon,
        nr_points: nr_points,
        time: duration,
        start_at: last_location.created_at,
        end_at: current_entry.created_at,
        total_distance: total_distance,
        distance_to_previous: distance
      }
    end
    aggregated_locations
  end

  private

  def distance_between_locations_in_m(entry1, entry2)
    lat1 = entry1.lat
    lon1 = entry1.lon
    lat2 = entry2.lat
    lon2 = entry2.lon

    distance = Haversine.distance(lat1, lon1, lat2, lon2)
    distance.to_meters
  end
end
