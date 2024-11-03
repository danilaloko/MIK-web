import folium

# Задайте начальные координаты и масштаб
lat, lon = 55.7558, 37.6176  # Москва, например
m = folium.Map(location=[lat, lon], zoom_start=20)

# Сохраните карту в HTML
m.save('map.html')
