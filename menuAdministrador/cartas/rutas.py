from geopy.geocoders import Nominatim
from geopy.distance import geodesic
import networkx as nx

# Inicializa el geocodificador
geolocator = Nominatim(user_agent="route_finder")

# Lista de direcciones
domicilios = ["Av. Juárez #33-A, Colonia Centro", "C. Federico Del Toro #180, Colonia Centro", "C. Carlos María De Bustamante #120, Colonia Centro"]

# Obtener las coordenadas geográficas
coords = []
for domicilio in domicilios:
    location = geolocator.geocode(domicilio)
    coords.append((location.latitude, location.longitude))

# Crear un grafo de rutas
G = nx.Graph()

# Añadir nodos y aristas (distancias) al grafo
for i in range(len(coords)):
    for j in range(i + 1, len(coords)):
        dist = geodesic(coords[i], coords[j]).kilometers
        G.add_edge(i, j, weight=dist)

# Encontrar la ruta más corta
shortest_path = nx.shortest_path(G, source=0, target=len(domicilios) - 1, weight='weight')
print("Ruta más corta:", shortest_path)
