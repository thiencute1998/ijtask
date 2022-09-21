<template>
  <div>
    <b-card-group deck class="mb-3">
      <b-card class="text-center small-box" body-class="p-1" style="background-color: #ed7d31; border-color: rgb(255 148 41); cursor: default !important;">
        <b-card-text>
          <h6>Tổng thu <span style="font-size:20px">{{overviewNumber.totalRevenue | convertNumberToText}}</span> tỷ</h6>
          <div class="row">
            <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-12">
              <b style="color: green;"><svg height="12" width="12" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0, 0, 400,400"><g id="svgg"><path id="path0" d="M68.299 197.679 C 68.353 306.402,68.549 395.509,68.734 395.694 C 68.919 395.879,74.184 395.978,80.435 395.915 L 91.800 395.800 91.903 324.030 C 91.960 284.557,92.054 252.212,92.113 252.154 C 92.171 252.095,95.148 251.962,98.729 251.857 C 107.273 251.608,107.228 251.635,107.808 246.300 L 108.058 244.000 112.329 243.952 C 125.094 243.810,123.799 244.280,123.804 239.795 C 123.809 235.636,122.940 236.050,131.814 235.972 C 146.401 235.844,147.236 235.579,147.529 230.977 C 147.732 227.786,147.107 228.028,155.486 227.903 C 163.879 227.778,163.728 227.862,163.940 223.188 L 164.085 220.000 169.942 219.942 C 173.164 219.910,176.357 219.921,177.038 219.966 C 179.281 220.115,185.321 218.857,186.100 218.078 C 186.529 217.649,187.016 216.238,187.275 214.673 L 187.718 212.000 190.759 211.996 C 203.701 211.980,203.489 212.062,203.890 206.900 L 204.115 204.000 209.358 203.956 C 219.044 203.876,219.533 203.840,220.083 203.166 C 220.367 202.817,221.352 202.318,222.271 202.058 C 224.253 201.497,225.860 199.753,226.235 197.756 C 226.570 195.969,226.136 196.071,234.313 195.857 C 243.320 195.620,243.224 195.677,243.808 190.300 L 244.058 188.000 248.329 187.952 C 261.106 187.810,259.799 188.295,259.803 183.698 C 259.804 181.766,259.979 180.219,260.203 180.159 C 260.421 180.101,264.740 179.967,269.800 179.860 C 282.298 179.598,283.049 179.299,283.492 174.414 L 283.711 172.000 287.556 171.968 C 289.670 171.951,292.506 171.963,293.858 171.995 C 299.285 172.122,300.000 171.487,300.000 166.540 L 300.000 164.000 305.900 163.942 C 309.145 163.910,312.357 163.921,313.038 163.966 C 315.281 164.115,321.321 162.857,322.100 162.078 C 322.529 161.649,323.016 160.238,323.275 158.673 L 323.718 156.000 326.759 155.996 C 339.701 155.980,339.489 156.062,339.890 150.900 L 340.115 148.000 345.358 147.956 C 355.044 147.876,355.533 147.840,356.083 147.166 C 356.367 146.817,357.352 146.318,358.271 146.058 C 360.253 145.497,361.860 143.753,362.235 141.756 C 362.570 139.969,362.136 140.071,370.313 139.857 C 380.281 139.595,379.801 140.195,379.798 128.000 C 379.794 115.718,380.269 116.298,370.186 116.284 C 362.566 116.274,362.812 116.352,362.063 113.729 C 361.513 111.805,359.816 110.195,357.910 109.789 C 357.189 109.636,356.264 109.216,355.853 108.855 C 355.236 108.314,354.294 108.218,350.453 108.303 C 347.894 108.359,344.520 108.301,342.956 108.174 L 340.113 107.942 339.866 105.030 C 339.444 100.030,339.774 100.199,330.564 100.263 L 323.728 100.310 323.292 97.495 C 323.014 95.699,322.577 94.399,322.084 93.906 C 321.000 92.822,314.747 91.887,310.721 92.207 C 309.027 92.341,305.944 92.327,303.869 92.174 L 300.097 91.898 299.913 88.728 C 299.646 84.106,299.828 84.205,291.484 84.178 C 287.698 84.166,284.400 84.121,284.156 84.078 C 283.911 84.035,283.613 82.914,283.492 81.586 C 283.048 76.690,282.219 76.371,269.483 76.198 C 258.896 76.054,259.851 76.428,259.823 72.410 C 259.791 68.038,260.108 68.200,251.613 68.200 C 243.358 68.200,244.046 68.491,243.656 64.837 C 243.187 60.452,240.876 59.611,231.109 60.276 C 227.157 60.545,226.624 60.318,226.226 58.198 C 225.860 56.247,224.237 54.499,222.271 53.938 C 221.352 53.676,220.264 53.178,219.853 52.831 C 219.248 52.321,218.215 52.220,214.453 52.303 C 211.894 52.359,208.520 52.301,206.956 52.174 L 204.113 51.942 203.866 49.030 C 203.444 44.030,203.774 44.199,194.564 44.263 L 187.728 44.310 187.292 41.495 C 187.014 39.699,186.577 38.399,186.084 37.906 C 185.000 36.822,178.747 35.887,174.721 36.207 C 173.027 36.341,169.944 36.327,167.869 36.174 L 164.097 35.898 163.913 32.728 C 163.646 28.106,163.828 28.205,155.484 28.178 C 151.698 28.166,148.400 28.121,148.156 28.078 C 147.911 28.035,147.613 26.914,147.492 25.586 C 147.048 20.690,146.219 20.371,133.483 20.198 C 122.896 20.054,123.851 20.428,123.823 16.410 C 123.791 12.038,124.108 12.200,115.613 12.200 C 107.350 12.200,108.047 12.499,107.651 8.788 C 107.233 4.880,106.851 4.597,101.569 4.275 C 99.315 4.138,96.440 4.134,95.179 4.265 C 90.917 4.711,90.000 4.307,90.000 1.982 L 90.000 0.000 87.221 -0.000 L 84.442 -0.000 84.295 2.100 L 84.148 4.200 84.074 2.100 C 84.011 0.311,83.885 -0.000,83.221 -0.000 C 82.566 -0.000,82.418 0.336,82.295 2.100 L 82.148 4.200 82.074 2.100 L 82.000 0.000 75.100 -0.000 L 68.200 -0.000 68.299 197.679 " stroke="none" fill="#0011ff" fill-rule="evenodd"></path><path id="path1" d="M82.120 2.000 C 82.120 3.210,82.195 3.705,82.287 3.100 C 82.378 2.495,82.378 1.505,82.287 0.900 C 82.195 0.295,82.120 0.790,82.120 2.000 M84.120 2.000 C 84.120 3.210,84.195 3.705,84.287 3.100 C 84.378 2.495,84.378 1.505,84.287 0.900 C 84.195 0.295,84.120 0.790,84.120 2.000 M68.109 5.600 C 68.109 6.590,68.187 6.995,68.282 6.500 C 68.378 6.005,68.378 5.195,68.282 4.700 C 68.187 4.205,68.109 4.610,68.109 5.600 M92.300 4.289 C 93.015 4.378,94.185 4.378,94.900 4.289 C 95.615 4.201,95.030 4.128,93.600 4.128 C 92.170 4.128,91.585 4.201,92.300 4.289 M68.198 199.800 C 68.198 304.080,68.244 346.680,68.300 294.467 C 68.356 242.254,68.356 156.934,68.300 104.867 C 68.244 52.800,68.198 95.520,68.198 199.800 M111.111 12.293 C 112.051 12.378,113.491 12.377,114.311 12.291 C 115.130 12.205,114.360 12.136,112.600 12.137 C 110.840 12.139,110.170 12.209,111.111 12.293 M130.500 20.291 C 131.325 20.378,132.675 20.378,133.500 20.291 C 134.325 20.205,133.650 20.134,132.000 20.134 C 130.350 20.134,129.675 20.205,130.500 20.291 M149.700 28.291 C 150.525 28.378,151.875 28.378,152.700 28.291 C 153.525 28.205,152.850 28.134,151.200 28.134 C 149.550 28.134,148.875 28.205,149.700 28.291 M169.911 36.293 C 170.851 36.378,172.291 36.377,173.111 36.291 C 173.930 36.205,173.160 36.136,171.400 36.137 C 169.640 36.139,168.970 36.209,169.911 36.293 M188.691 44.289 C 189.401 44.377,190.661 44.379,191.491 44.292 C 192.321 44.205,191.740 44.133,190.200 44.131 C 188.660 44.129,187.981 44.200,188.691 44.289 M208.300 52.293 C 209.235 52.377,210.765 52.377,211.700 52.293 C 212.635 52.209,211.870 52.140,210.000 52.140 C 208.130 52.140,207.365 52.209,208.300 52.293 M228.300 60.289 C 229.015 60.378,230.185 60.378,230.900 60.289 C 231.615 60.201,231.030 60.128,229.600 60.128 C 228.170 60.128,227.585 60.201,228.300 60.289 M247.111 68.293 C 248.051 68.378,249.491 68.377,250.311 68.291 C 251.130 68.205,250.360 68.136,248.600 68.137 C 246.840 68.139,246.170 68.209,247.111 68.293 M266.500 76.291 C 267.325 76.378,268.675 76.378,269.500 76.291 C 270.325 76.205,269.650 76.134,268.000 76.134 C 266.350 76.134,265.675 76.205,266.500 76.291 M285.700 84.291 C 286.525 84.378,287.875 84.378,288.700 84.291 C 289.525 84.205,288.850 84.134,287.200 84.134 C 285.550 84.134,284.875 84.205,285.700 84.291 M305.911 92.293 C 306.851 92.378,308.291 92.377,309.111 92.291 C 309.930 92.205,309.160 92.136,307.400 92.137 C 305.640 92.139,304.970 92.209,305.911 92.293 M324.691 100.289 C 325.401 100.377,326.661 100.379,327.491 100.292 C 328.321 100.205,327.740 100.133,326.200 100.131 C 324.660 100.129,323.981 100.200,324.691 100.289 M344.300 108.293 C 345.235 108.377,346.765 108.377,347.700 108.293 C 348.635 108.209,347.870 108.140,346.000 108.140 C 344.130 108.140,343.365 108.209,344.300 108.293 M364.300 116.289 C 365.015 116.378,366.185 116.378,366.900 116.289 C 367.615 116.201,367.030 116.128,365.600 116.128 C 364.170 116.128,363.585 116.201,364.300 116.289 M68.093 392.400 C 68.093 393.170,68.175 393.485,68.276 393.100 C 68.376 392.715,68.376 392.085,68.276 391.700 C 68.175 391.315,68.093 391.630,68.093 392.400 " stroke="none" fill="#5094e4" fill-rule="evenodd"></path><path id="path2" d="M82.120 2.000 C 82.120 3.210,82.195 3.705,82.287 3.100 C 82.378 2.495,82.378 1.505,82.287 0.900 C 82.195 0.295,82.120 0.790,82.120 2.000 M84.120 2.000 C 84.120 3.210,84.195 3.705,84.287 3.100 C 84.378 2.495,84.378 1.505,84.287 0.900 C 84.195 0.295,84.120 0.790,84.120 2.000 M68.109 5.600 C 68.109 6.590,68.187 6.995,68.282 6.500 C 68.378 6.005,68.378 5.195,68.282 4.700 C 68.187 4.205,68.109 4.610,68.109 5.600 M92.300 4.289 C 93.015 4.378,94.185 4.378,94.900 4.289 C 95.615 4.201,95.030 4.128,93.600 4.128 C 92.170 4.128,91.585 4.201,92.300 4.289 M68.198 199.800 C 68.198 304.080,68.244 346.680,68.300 294.467 C 68.356 242.254,68.356 156.934,68.300 104.867 C 68.244 52.800,68.198 95.520,68.198 199.800 M111.111 12.293 C 112.051 12.378,113.491 12.377,114.311 12.291 C 115.130 12.205,114.360 12.136,112.600 12.137 C 110.840 12.139,110.170 12.209,111.111 12.293 M130.500 20.291 C 131.325 20.378,132.675 20.378,133.500 20.291 C 134.325 20.205,133.650 20.134,132.000 20.134 C 130.350 20.134,129.675 20.205,130.500 20.291 M149.700 28.291 C 150.525 28.378,151.875 28.378,152.700 28.291 C 153.525 28.205,152.850 28.134,151.200 28.134 C 149.550 28.134,148.875 28.205,149.700 28.291 M169.911 36.293 C 170.851 36.378,172.291 36.377,173.111 36.291 C 173.930 36.205,173.160 36.136,171.400 36.137 C 169.640 36.139,168.970 36.209,169.911 36.293 M188.691 44.289 C 189.401 44.377,190.661 44.379,191.491 44.292 C 192.321 44.205,191.740 44.133,190.200 44.131 C 188.660 44.129,187.981 44.200,188.691 44.289 M208.300 52.293 C 209.235 52.377,210.765 52.377,211.700 52.293 C 212.635 52.209,211.870 52.140,210.000 52.140 C 208.130 52.140,207.365 52.209,208.300 52.293 M228.300 60.289 C 229.015 60.378,230.185 60.378,230.900 60.289 C 231.615 60.201,231.030 60.128,229.600 60.128 C 228.170 60.128,227.585 60.201,228.300 60.289 M247.111 68.293 C 248.051 68.378,249.491 68.377,250.311 68.291 C 251.130 68.205,250.360 68.136,248.600 68.137 C 246.840 68.139,246.170 68.209,247.111 68.293 M266.500 76.291 C 267.325 76.378,268.675 76.378,269.500 76.291 C 270.325 76.205,269.650 76.134,268.000 76.134 C 266.350 76.134,265.675 76.205,266.500 76.291 M285.700 84.291 C 286.525 84.378,287.875 84.378,288.700 84.291 C 289.525 84.205,288.850 84.134,287.200 84.134 C 285.550 84.134,284.875 84.205,285.700 84.291 M305.911 92.293 C 306.851 92.378,308.291 92.377,309.111 92.291 C 309.930 92.205,309.160 92.136,307.400 92.137 C 305.640 92.139,304.970 92.209,305.911 92.293 M324.691 100.289 C 325.401 100.377,326.661 100.379,327.491 100.292 C 328.321 100.205,327.740 100.133,326.200 100.131 C 324.660 100.129,323.981 100.200,324.691 100.289 M344.300 108.293 C 345.235 108.377,346.765 108.377,347.700 108.293 C 348.635 108.209,347.870 108.140,346.000 108.140 C 344.130 108.140,343.365 108.209,344.300 108.293 M364.300 116.289 C 365.015 116.378,366.185 116.378,366.900 116.289 C 367.615 116.201,367.030 116.128,365.600 116.128 C 364.170 116.128,363.585 116.201,364.300 116.289 M68.093 392.400 C 68.093 393.170,68.175 393.485,68.276 393.100 C 68.376 392.715,68.376 392.085,68.276 391.700 C 68.175 391.315,68.093 391.630,68.093 392.400 " stroke="none" fill="#5094e4" fill-rule="evenodd"></path><path id="path3" d="M82.120 2.000 C 82.120 3.210,82.195 3.705,82.287 3.100 C 82.378 2.495,82.378 1.505,82.287 0.900 C 82.195 0.295,82.120 0.790,82.120 2.000 M84.120 2.000 C 84.120 3.210,84.195 3.705,84.287 3.100 C 84.378 2.495,84.378 1.505,84.287 0.900 C 84.195 0.295,84.120 0.790,84.120 2.000 M68.109 5.600 C 68.109 6.590,68.187 6.995,68.282 6.500 C 68.378 6.005,68.378 5.195,68.282 4.700 C 68.187 4.205,68.109 4.610,68.109 5.600 M92.300 4.289 C 93.015 4.378,94.185 4.378,94.900 4.289 C 95.615 4.201,95.030 4.128,93.600 4.128 C 92.170 4.128,91.585 4.201,92.300 4.289 M68.198 199.800 C 68.198 304.080,68.244 346.680,68.300 294.467 C 68.356 242.254,68.356 156.934,68.300 104.867 C 68.244 52.800,68.198 95.520,68.198 199.800 M111.111 12.293 C 112.051 12.378,113.491 12.377,114.311 12.291 C 115.130 12.205,114.360 12.136,112.600 12.137 C 110.840 12.139,110.170 12.209,111.111 12.293 M130.500 20.291 C 131.325 20.378,132.675 20.378,133.500 20.291 C 134.325 20.205,133.650 20.134,132.000 20.134 C 130.350 20.134,129.675 20.205,130.500 20.291 M149.700 28.291 C 150.525 28.378,151.875 28.378,152.700 28.291 C 153.525 28.205,152.850 28.134,151.200 28.134 C 149.550 28.134,148.875 28.205,149.700 28.291 M169.911 36.293 C 170.851 36.378,172.291 36.377,173.111 36.291 C 173.930 36.205,173.160 36.136,171.400 36.137 C 169.640 36.139,168.970 36.209,169.911 36.293 M188.691 44.289 C 189.401 44.377,190.661 44.379,191.491 44.292 C 192.321 44.205,191.740 44.133,190.200 44.131 C 188.660 44.129,187.981 44.200,188.691 44.289 M208.300 52.293 C 209.235 52.377,210.765 52.377,211.700 52.293 C 212.635 52.209,211.870 52.140,210.000 52.140 C 208.130 52.140,207.365 52.209,208.300 52.293 M228.300 60.289 C 229.015 60.378,230.185 60.378,230.900 60.289 C 231.615 60.201,231.030 60.128,229.600 60.128 C 228.170 60.128,227.585 60.201,228.300 60.289 M247.111 68.293 C 248.051 68.378,249.491 68.377,250.311 68.291 C 251.130 68.205,250.360 68.136,248.600 68.137 C 246.840 68.139,246.170 68.209,247.111 68.293 M266.500 76.291 C 267.325 76.378,268.675 76.378,269.500 76.291 C 270.325 76.205,269.650 76.134,268.000 76.134 C 266.350 76.134,265.675 76.205,266.500 76.291 M285.700 84.291 C 286.525 84.378,287.875 84.378,288.700 84.291 C 289.525 84.205,288.850 84.134,287.200 84.134 C 285.550 84.134,284.875 84.205,285.700 84.291 M305.911 92.293 C 306.851 92.378,308.291 92.377,309.111 92.291 C 309.930 92.205,309.160 92.136,307.400 92.137 C 305.640 92.139,304.970 92.209,305.911 92.293 M324.691 100.289 C 325.401 100.377,326.661 100.379,327.491 100.292 C 328.321 100.205,327.740 100.133,326.200 100.131 C 324.660 100.129,323.981 100.200,324.691 100.289 M344.300 108.293 C 345.235 108.377,346.765 108.377,347.700 108.293 C 348.635 108.209,347.870 108.140,346.000 108.140 C 344.130 108.140,343.365 108.209,344.300 108.293 M364.300 116.289 C 365.015 116.378,366.185 116.378,366.900 116.289 C 367.615 116.201,367.030 116.128,365.600 116.128 C 364.170 116.128,363.585 116.201,364.300 116.289 M68.093 392.400 C 68.093 393.170,68.175 393.485,68.276 393.100 C 68.376 392.715,68.376 392.085,68.276 391.700 C 68.175 391.315,68.093 391.630,68.093 392.400 " stroke="none" fill="#5094e4" fill-rule="evenodd"></path><path id="path4" d="M82.120 2.000 C 82.120 3.210,82.195 3.705,82.287 3.100 C 82.378 2.495,82.378 1.505,82.287 0.900 C 82.195 0.295,82.120 0.790,82.120 2.000 M84.120 2.000 C 84.120 3.210,84.195 3.705,84.287 3.100 C 84.378 2.495,84.378 1.505,84.287 0.900 C 84.195 0.295,84.120 0.790,84.120 2.000 M68.109 5.600 C 68.109 6.590,68.187 6.995,68.282 6.500 C 68.378 6.005,68.378 5.195,68.282 4.700 C 68.187 4.205,68.109 4.610,68.109 5.600 M92.300 4.289 C 93.015 4.378,94.185 4.378,94.900 4.289 C 95.615 4.201,95.030 4.128,93.600 4.128 C 92.170 4.128,91.585 4.201,92.300 4.289 M68.198 199.800 C 68.198 304.080,68.244 346.680,68.300 294.467 C 68.356 242.254,68.356 156.934,68.300 104.867 C 68.244 52.800,68.198 95.520,68.198 199.800 M111.111 12.293 C 112.051 12.378,113.491 12.377,114.311 12.291 C 115.130 12.205,114.360 12.136,112.600 12.137 C 110.840 12.139,110.170 12.209,111.111 12.293 M130.500 20.291 C 131.325 20.378,132.675 20.378,133.500 20.291 C 134.325 20.205,133.650 20.134,132.000 20.134 C 130.350 20.134,129.675 20.205,130.500 20.291 M149.700 28.291 C 150.525 28.378,151.875 28.378,152.700 28.291 C 153.525 28.205,152.850 28.134,151.200 28.134 C 149.550 28.134,148.875 28.205,149.700 28.291 M169.911 36.293 C 170.851 36.378,172.291 36.377,173.111 36.291 C 173.930 36.205,173.160 36.136,171.400 36.137 C 169.640 36.139,168.970 36.209,169.911 36.293 M188.691 44.289 C 189.401 44.377,190.661 44.379,191.491 44.292 C 192.321 44.205,191.740 44.133,190.200 44.131 C 188.660 44.129,187.981 44.200,188.691 44.289 M208.300 52.293 C 209.235 52.377,210.765 52.377,211.700 52.293 C 212.635 52.209,211.870 52.140,210.000 52.140 C 208.130 52.140,207.365 52.209,208.300 52.293 M228.300 60.289 C 229.015 60.378,230.185 60.378,230.900 60.289 C 231.615 60.201,231.030 60.128,229.600 60.128 C 228.170 60.128,227.585 60.201,228.300 60.289 M247.111 68.293 C 248.051 68.378,249.491 68.377,250.311 68.291 C 251.130 68.205,250.360 68.136,248.600 68.137 C 246.840 68.139,246.170 68.209,247.111 68.293 M266.500 76.291 C 267.325 76.378,268.675 76.378,269.500 76.291 C 270.325 76.205,269.650 76.134,268.000 76.134 C 266.350 76.134,265.675 76.205,266.500 76.291 M285.700 84.291 C 286.525 84.378,287.875 84.378,288.700 84.291 C 289.525 84.205,288.850 84.134,287.200 84.134 C 285.550 84.134,284.875 84.205,285.700 84.291 M305.911 92.293 C 306.851 92.378,308.291 92.377,309.111 92.291 C 309.930 92.205,309.160 92.136,307.400 92.137 C 305.640 92.139,304.970 92.209,305.911 92.293 M324.691 100.289 C 325.401 100.377,326.661 100.379,327.491 100.292 C 328.321 100.205,327.740 100.133,326.200 100.131 C 324.660 100.129,323.981 100.200,324.691 100.289 M344.300 108.293 C 345.235 108.377,346.765 108.377,347.700 108.293 C 348.635 108.209,347.870 108.140,346.000 108.140 C 344.130 108.140,343.365 108.209,344.300 108.293 M364.300 116.289 C 365.015 116.378,366.185 116.378,366.900 116.289 C 367.615 116.201,367.030 116.128,365.600 116.128 C 364.170 116.128,363.585 116.201,364.300 116.289 M68.093 392.400 C 68.093 393.170,68.175 393.485,68.276 393.100 C 68.376 392.715,68.376 392.085,68.276 391.700 C 68.175 391.315,68.093 391.630,68.093 392.400 " stroke="none" fill="#ffe100" fill-rule="evenodd"></path></g></svg></b>{{(Math.abs((overviewNumber.totalRevenue) / overviewNumber.totalEstimate)) | formatPercent}}% <br class="d-none d-sm-none d-md-none d-lg-none d-xl-block">so với dự toán
            </div>
            <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-12">
                <b v-if="overviewNumber.totalRevenue >= overviewNumber.totalRevenuePrePeriod" style="color: green;"><svg height="20" width="20" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-caret-up fa-w-10"><path fill="currentColor" d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path></svg></b>
                <b v-else style="color: red;"><svg height="20" width="20" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-caret-down fa-w-10"><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path></svg></b>
                {{(Math.abs((overviewNumber.totalRevenue - overviewNumber.totalRevenuePrePeriod) / overviewNumber.totalRevenuePrePeriod)) | formatPercent}}% <br class="d-none d-sm-none d-md-none d-lg-none d-xl-block">so với cùng kỳ
            </div>
          </div>
        </b-card-text>
      </b-card>
      <b-card class="text-center small-box" body-class="p-1" style="background-color: #f4b183; border-color: rgb(255 148 41); cursor: default !important;">
        <b-card-text>
          <h6><span style="font-size:20px">{{overviewNumber.taxAgency | convertNumberToText}}</span> tỷ</h6>
          <div class="row">
            <div class="col-24 mb-2">
              <b style="color: rgb(197, 90, 17);">≈</b>{{(overviewNumber.taxAgency / overviewNumber.totalRevenue) | formatPercent}}%
              <br>Cơ quan thuế thu
            </div>
          </div>
        </b-card-text>
      </b-card>
      <b-card class="text-center small-box" body-class="p-1" style="background-color: #f8cbad; border-color: rgb(255 148 41); cursor: default !important;">
        <b-card-text>
          <h6><span style="font-size:20px">{{overviewNumber.customsAgency | convertNumberToText}}</span> tỷ</h6>
          <div class="row">
            <div class="col-24 mb-2">
              <b style="color: rgb(197, 90, 17);">≈</b>{{(overviewNumber.customsAgency / overviewNumber.totalRevenue) | formatPercent}}%
              <br>Hải quan thu
            </div>
          </div>
        </b-card-text>
      </b-card>
      <b-card class="text-center small-box" body-class="p-1" style="background-color: #fbe5d6; border-color: rgb(255 148 41); cursor: default !important;">
        <b-card-text>
          <h6><span style="font-size:20px">{{overviewNumber.otherAgency | convertNumberToText}}</span> tỷ</h6>
          <div class="row">
            <div class="col-24 mb-2">
              <b style="color: rgb(197, 90, 17);">≈</b>{{(overviewNumber.otherAgency / overviewNumber.totalRevenue) | formatPercent}}%
              <br>Cơ quan khác thu
            </div>
          </div>
        </b-card-text>
      </b-card>
    </b-card-group>

    <div class="row">
      <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-14" style="padding-top: 30px">
        <BarChart3 :filter="filter" :overviewNumber="overviewNumber"></BarChart3>
      </div>

      <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-9" style="padding-top: 30px">
        <line-chart :filter="filter" :overview-number="overviewNumber"></line-chart>
      </div>
    </div>
    <div class="row">
      <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-14" style="padding-top: 30px" v-show="filter.UserType !== 2">
        <BarChart2 :filter="filter" :overviewNumber="overviewNumber"></BarChart2>
      </div>
      <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-24" style="padding-top: 30px" v-show="filter.UserType === 2">
        <BarChart22 :filter="filter" :overviewNumber="overviewNumber"></BarChart22>
      </div>
      <div class="col-24 col-sm-24 col-md-18 col-lg-18 m-lg-auto m-md-auto col-xl-9" style="padding-top: 30px" v-if="filter.UserType !== 2">
        <pie-chart :filter="filter"></pie-chart>
      </div>
    </div>
    <div class="row">
      <div class="col-24 col-sm-24 col-md-24 col-lg-24 col-xl-24" style="padding-left: 20px; padding-top: 30px;">
        <BarChart :filter="filter" :overviewNumber="overviewNumber"></BarChart>
      </div>
    </div>
  </div>
</template>
<style>
  .text-to-voice {
    position: fixed;
    right: 40px;
    bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    border-radius: 20px;
    display: none;
  }
  .text-to-voice audio{
    height: 48px;
  }
</style>
<script>
  import ApiService from '@/services/api.service';
  import Chart from 'chart.js';
  import 'chartjs-plugin-labels';
  import 'chartjs-plugin-zoom';
  import LineChart from "./TotalRevenues/LineChart";
  import PieChart from "./TotalRevenues/PieChart";
  import BarChart3 from './TotalRevenues/BarChart3';
  import BarChart from './TotalRevenues/BarChart';
  import BarChart2 from './TotalRevenues/BarChart2';
  import BarChart22 from './TotalRevenues/BarChart22';


  export default {
    name: 'dashboard-module-state-budget-revenues',
    props: {
      filter: [Array, Object]
    },
    data: function () {
      return {
        chartType: 1,
        optionDataBar: {
          labels: ['Thành phố Biên Hòa', 'Thành phố Long Khánh', 'Huyện Long Thành',
            'Huyện Nhơn Trạch', 'Huyện Vĩnh Cửu', 'Huyện Trảng Bom', 'Huyện Thống Nhất',
            'Huyện Cẩm Mỹ', 'Huyện Xuân Lộc', 'Huyện Định quán', 'Huyện Tân Phú'
          ],
          datasets: [
            {
              label: "Dự toán",
              backgroundColor: "#2eadd3",
              fill: false,
              data: [9, 9, 3, 8, 5, 6, 5, 6, 7, 8, 8],
            },
            {
              label: "Đã thu",
              backgroundColor: "#ed7d31",
              fill: false,
              data: [8, 8, 4, 6, 7, 8, 8, 8, 5, 6, 9]
            },
          ]
        },
        optionDataBar2: {
          labels: [
            'Thu nội địa',
            'Thu dầu thô và khí thiên nhiên',
            'Thu từ xuất nhập khẩu',
            'Thu bổ sung từ ngân sách cấp trên',
            'Thu từ quỹ dự trữ tài chính',
            'Thu kết dư',
            'Thu chuyển nguồn từ năm trước',
            'Thu huy động đóng góp',
            'Thu viện trợ',
            'Thu vay nợ ',
            'Thu từ ngân sách cấp dưới nộp lên',
            'Tạm thu ngân sách',
          ],
          datasets: [
            {
              label: "Dự toán",
              backgroundColor: "#2eadd3",
              data: [29, 29, 23, 28, 26, 25, 26, 23, 28, 26, 25, 26],
            },
            {
              label: "Đã thu",
              backgroundColor: "#ed7d31",
              data: [28, 28, 24, 26, 28, 25, 28, 28, 28, 24, 26, 28]
            },
          ]
        },
        optionDataBar3: {
          labels: [
            'Thu thuế',
            'Thu phí, lệ phí',
            'Thu dầu thô và khí thiên nhiên',
            'Vốn góp & các khoản đầu tư của NN',
            'Thu từ viện trợ không hoàn lại',
            'Thu vay nợ ',
            'Thu huy động đóng góp',
            'Thu khác'
          ],
          datasets: [
            {
              label: "Dự toán",
              backgroundColor: "#2eadd3",
              fill: true,
              data: [29, 29, 23, 28, 26, 25, 26, 28],
            },
            {
              label: "Đã thu ",
              backgroundColor: "#ed7d31",
              fill: true,
              data: [28, 28, 24, 26, 28, 25, 28, 23]
            },
          ]
        },
        optionDataLineChar: {
          labels: ['Thu NSNN theo cơ cấu thu (Tỷ dồng)'],
          datasets: [{
            label: '',
            data: [],
            backgroundColor: [],
            borderColor: [],
            borderWidth: null
          }]
        },
        overviewNumber : {
          totalRevenue: 16622,
          totalEstimate: 22409,
          compareEstimate: 110,
          compareSamePeriod: 21.97,
          totalRevenuePrePeriod: 21302,
          taxAgency: 9382,
          customsAgency: 3365,
          otherAgency: 3875
        }
      }
    },
    components: {
      LineChart,
      PieChart,
      BarChart3,
      BarChart,
      BarChart2,
      BarChart22,
    },
    mounted () {
      let self = this;
    },
    methods: {
      changeChart(type){
        let datasetsPie = [];
        let labelsPie = [];
        let titlePie = '';
        let currentDataBar = this.optionDataBar.datasets[0].data;
        this.optionDataBar.datasets[0].data = [];
        let self = this;
        switch (type) {
          case 1:
            datasetsPie = [12, 30, 3, 5];
            labelsPie = ['Thu nội địa', 'Thu XNK', 'Thu viện trợ', 'Thu khác'];
            this.chartType = 1;
            titlePie = 'Dự toán thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 2:
            datasetsPie = [8, 23, 6, 9, 19, 25];
            labelsPie = ['Thu thường xuyên', 'Thu đầu tư phát triển', 'Thu cải cách tiền lương, tinh giảm biên chế', 'Thu viện trợ', 'Thu trả nợ', 'Thu khác'];
            this.chartType = 2;
            titlePie = 'Dự toán thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 3:
            datasetsPie = [11, 55, 32, 5];
            labelsPie = ['Thu nội địa', 'Thu XNK', 'Thu viện trợ', 'Thu khác'];
            this.chartType = 3;
            titlePie = 'Tổng thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 4:
            datasetsPie = [88, 32, 56, 78, 64];
            labelsPie = ['Thu thường xuyên', 'Thu đầu tư phát triển', 'Thu cải cách tiền lương, tinh giảm biên chế', 'Thu trả nợ', 'Thu khác'];
            this.chartType = 4;
            titlePie = 'Tổng thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 5:
            datasetsPie = [9, 45, 54, 6];
            labelsPie = ['Thu nội địa', 'Thu XNK', 'Thu viện trợ', 'Thu khác'];
            this.chartType = 5;
            titlePie = 'Quyết toán thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          case 6:
            datasetsPie = [66, 33, 99, 50, 60];
            labelsPie = ['Thu thường xuyên', 'Thu đầu tư phát triển', 'Thu cải cách tiền lương, tinh giảm biên chế', 'Thu trả nợ', 'Thu khác'];
            this.chartType = 6;
            titlePie = 'Quyết toán thu';
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            break;
          default:
            datasetsPie = [78, 45, 67, 54];
            labelsPie = ['Thu nội địa', 'Thu XNK', 'Thu viện trợ', 'Thu khác'];
            titlePie = 'Dự toán thu';
            this.optionDataBar.datasets[0].data = [];
            _.forEach(currentDataBar, function (value, key) {
              let randomNumber = _.random(0, 100);
              self.optionDataBar.datasets[0].data.push(randomNumber);
            });
            this.chartType = 1;
        }
      },
      updateData(chart, labels = [], datasets, title = '') {
        if (labels.length) {
          chart.data.labels = labels;
        }
        chart.data.datasets.forEach((item, key) => {
          if (datasets && datasets[key] && datasets[key].data) {
            item.data = datasets[key].data;
          }
          if (datasets && datasets[key] && datasets[key].backgroundColor) {
            item.backgroundColor = datasets[key].backgroundColor;
          }
          if (datasets && datasets[key] && datasets[key].borderColor) {
            item.borderColor = datasets[key].borderColor;
          }
        });
        if (title) {
          chart.options.title.title = title;
          chart.config.options.title.text = title;
          chart.config.options.title.title = title;
        }
        chart.update();

        // chart.data.labels.push(labels);
        // chart.data.datasets.forEach((datasets) => {
        //   dataset.data.push(data);
        // });
      },
      removeData(chart) {
        chart.data.labels.pop();
        chart.data.datasets.forEach((dataset) => {
          dataset.data.pop();
        });
        chart.update();
      },
      getDistrict(){
        let self = this;
        let requestData = {
          url: 'dashboard/api/dashboard/get-district',
          method: 'post',
          data: {
            ProvinceID: this.filter.ProvinceID
          }
        };
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {

          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.optionDataBar.labels = [];
            _.forEach(responsesData.data, function (district, key) {
              self.optionDataBar.labels.push(district.DistrictName);
            });
            _.forEach(self.optionDataBar.datasets, function (dataset, keyDataset) {
              self.optionDataBar.datasets[keyDataset].data = [];
              for (let i = 0; i < responsesData.data.length; i++) {
                let randomNumber = _.random(0, 100);
                self.optionDataBar.datasets[keyDataset].data.push(randomNumber);
              }
            });
            // this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.datasets, '');
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      getCommune(){
        let self = this;
        let requestData = {
          url: 'dashboard/api/dashboard/get-commune',
          method: 'post',
          data: {
            DistrictID: this.filter.DistrictID
          }
        };
        this.$store.commit('isLoading', true);
        ApiService.setHeader();
        ApiService.customRequest(requestData).then((responses) => {

          let responsesData = responses.data;
          if (responsesData.status === 1) {
            self.optionDataBar.labels = [];
            _.forEach(responsesData.data, function (commune, key) {
              self.optionDataBar.labels.push(commune.CommuneName);
            });
            _.forEach(self.optionDataBar.datasets, function (dataset, keyDataset) {
              self.optionDataBar.datasets[keyDataset].data = [];
              for (let i = 0; i < responsesData.data.length; i++) {
                let randomNumber = _.random(0, 100);
                self.optionDataBar.datasets[keyDataset].data.push(randomNumber);
              }
            });
            // this.updateData(BarChart, self.optionDataBar.labels, self.optionDataBar.datasets, '');
          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      changeFilter() {
        let self = this;
        let ratePeriod = 1;
        if (this.filter.PeriodID === 6) {
          ratePeriod = _.random(0.3, 0.7);
        }else if (this.filter.PeriodID === 7) {
          ratePeriod = _.random(0.5, 0.9);
        }else if (this.filter.PeriodID === 8) {
          ratePeriod = _.random(2.5, 3.5);
        }else if (this.filter.PeriodID === 9) {
          ratePeriod = _.random(3.5, 8.5);
        }else if (this.filter.PeriodID === 10) {
          ratePeriod = _.random(7.5, 13.5);
        }

        let rateDistrict = 1;
        let rateCommune = 1;

        let rateRevenueCate = _.random(0.2, 0.8);

        if (this.filter.PeriodID === 1 && Number(this.filter.PeriodValue) > 2020){
          this.overviewNumber.totalRevenue = 0;
          this.overviewNumber.compareSamePeriod = 0;
          this.overviewNumber.taxAgency = 0;
          this.overviewNumber.customsAgency = 0;
          this.overviewNumber.otherAgency = 0;
          return;
        }

        if (!this.overviewNumber.totalRevenue) {
          this.overviewNumber.totalRevenue = _.random(10000, 33650);
        }else if (this.filter.ProvinceID === 36 && !this.filter.DistrictID && !this.filter.CommuneID) {
          if (!this.filter.RevenueCateID) {
            return;
          }
        }

        if (this.filter.ProvinceID && !this.filter.DistrictID && !this.filter.CommuneID) {
          this.overviewNumber.totalRevenue = this.overviewNumber.totalRevenue * ratePeriod;
        }else if (this.filter.ProvinceID && this.filter.DistrictID && !this.filter.CommuneID) {
          rateDistrict = _.random(1 / 10, 1 / 25);
          this.overviewNumber.totalRevenue = this.overviewNumber.totalRevenue * rateDistrict * ratePeriod;

        }else if (this.filter.CommuneID) {
          rateDistrict = _.random(1 / 10, 1 / 25);
          rateCommune = _.random(1 / 40, 1 / 70);
          this.overviewNumber.totalRevenue = this.overviewNumber.totalRevenue * rateDistrict * rateCommune * ratePeriod;
        }

        if (this.filter.RevenueCateID) {
          this.overviewNumber.totalRevenue *= rateRevenueCate;
        }

        this.overviewNumber.totalRevenue = Math.floor(this.overviewNumber.totalRevenue);
        this.overviewNumber.totalEstimate = _.random(this.overviewNumber.totalRevenue + 1000 * ratePeriod * rateDistrict * rateCommune, this.overviewNumber.totalRevenue + 2000 * ratePeriod * rateDistrict * rateCommune);
        this.overviewNumber.totalEstimate = Math.floor(this.overviewNumber.totalEstimate);
        this.overviewNumber.totalRevenuePrePeriod = _.random(this.overviewNumber.totalRevenue - 1000 * ratePeriod * rateDistrict * rateCommune, this.overviewNumber.totalRevenue + 1000 * ratePeriod * rateDistrict * rateCommune);
        this.overviewNumber.totalRevenuePrePeriod = Math.floor(this.overviewNumber.totalRevenuePrePeriod);
        this.overviewNumber.compareSamePeriod = _.random(0.1, 5.8);
        this.overviewNumber.compareSamePeriod = this.overviewNumber.compareSamePeriod.toFixed(2);
        this.overviewNumber.taxAgency = _.random((this.overviewNumber.totalRevenue / 3) - 1000 * ratePeriod * rateDistrict * rateCommune, (this.overviewNumber.totalRevenue / 3) + 1000 * ratePeriod * rateDistrict * rateCommune);
        this.overviewNumber.taxAgency = Math.floor(this.overviewNumber.taxAgency);
        this.overviewNumber.customsAgency = _.random((this.overviewNumber.totalRevenue / 4) - 1000 * ratePeriod * rateDistrict * rateCommune, (this.overviewNumber.totalRevenue / 4) + 1000 * ratePeriod * rateDistrict * rateCommune);
        this.overviewNumber.customsAgency = Math.floor(this.overviewNumber.customsAgency);
        this.overviewNumber.otherAgency = this.overviewNumber.totalRevenue - this.overviewNumber.taxAgency - this.overviewNumber.customsAgency;

      },
      resetOverviewNumber() {
        if (this.filter.ProvinceID === 36) {
          if (!this.filter.DistrictID && !this.filter.CommuneID) {
            let ratePeriod = 1;
            if (this.filter.PeriodID === 6) {
              ratePeriod = _.random(0.3, 0.7);
            }else if (this.filter.PeriodID === 7) {
              ratePeriod = _.random(0.5, 0.9);
            }else if (this.filter.PeriodID === 8) {
              ratePeriod = _.random(2.5, 3.5);
            }else if (this.filter.PeriodID === 9) {
              ratePeriod = _.random(3.5, 8.5);
            }else if (this.filter.PeriodID === 10) {
              ratePeriod = _.random(7.5, 13.5);
            }

            this.overviewNumber.totalRevenue = 16622 * ratePeriod;
            this.overviewNumber.totalRevenue = Math.floor(this.overviewNumber.totalRevenue);
            if (ratePeriod === 1) {
              this.overviewNumber.totalEstimate = 22409 * ratePeriod;
              this.overviewNumber.totalRevenuePrePeriod = 21409 * ratePeriod;
            } else {
              this.overviewNumber.totalEstimate = _.random(16622 + 500 * ratePeriod, 16622 + 1000 * ratePeriod);
              this.overviewNumber.totalEstimate = this.overviewNumber.totalEstimate * ratePeriod;

              this.overviewNumber.totalRevenuePrePeriod = _.random(16622 - 500 * ratePeriod, 16622 + 500 * ratePeriod);
              this.overviewNumber.totalRevenuePrePeriod = this.overviewNumber.totalRevenuePrePeriod * ratePeriod;
            }
            this.overviewNumber.totalEstimate = Math.floor(this.overviewNumber.totalEstimate);
            this.overviewNumber.totalRevenuePrePeriod = Math.floor(this.overviewNumber.totalRevenuePrePeriod);
            this.overviewNumber.compareSamePeriod = 2;
            this.overviewNumber.taxAgency = 6550 * ratePeriod;
            this.overviewNumber.taxAgency = Math.floor(this.overviewNumber.taxAgency);
            this.overviewNumber.customsAgency = 5951 * ratePeriod;
            this.overviewNumber.customsAgency = Math.floor(this.overviewNumber.customsAgency);
            this.overviewNumber.otherAgency = this.overviewNumber.totalRevenue - this.overviewNumber.taxAgency - this.overviewNumber.customsAgency;
          }else {
            this.overviewNumber.totalRevenue = 16622;
          }
        }else {
          this.overviewNumber.totalRevenue = 0;
        }

        if (this.filter.PeriodID === 1 && this.filter.PeriodValue != 2020) {
          this.overviewNumber.totalRevenue = 0;
        }else if (this.filter.PeriodID === 1 && this.filter.PeriodValue == 2020){
          this.overviewNumber.totalRevenue = 16622;
          this.overviewNumber.totalEstimate = 22409;
          this.overviewNumber.totalRevenuePrePeriod = 21302;
          this.overviewNumber.compareSamePeriod = 2;
          this.overviewNumber.taxAgency = 6550;
          this.overviewNumber.customsAgency = 5951;
          this.overviewNumber.otherAgency = this.overviewNumber.totalRevenue - this.overviewNumber.taxAgency - this.overviewNumber.customsAgency;
        }
      }
    },
    watch: {
      'filter.UserType'(){
        this.filter.ProvinceName = 'Tỉnh Nam Định';
        this.filter.ProvinceNo = '420000';
        this.filter.ProvinceID = 36;
        this.filter.DistrictName = '';
        this.filter.DistrictNo = '';
        this.filter.DistrictID = null;
        this.filter.CommuneName = '';
        this.filter.CommuneID = null;
        this.filter.CommuneNo = '';
        this.filter.CompanyName = '';
        this.filter.CompanyNo = '';
        this.filter.CompanyID = null;
        this.filter.SectorID = null;
        this.filter.SectorNo = '';
        this.filter.SectorName = '';
        this.filter.SectorNo = '';
        this.filter.SectorID = null;
        this.filter.PeriodID = null;
        this.filter.PeriodNo = '';
        this.filter.PeriodName = '';
        this.filter.PeriodValue = '';
        this.filter.PeriodValueName = '';
        this.filter.FromDate = null;
        this.filter.ToDate = null;

        this.overviewNumber.totalRevenue = 16622;
        this.overviewNumber.totalEstimate = 22409;
        this.overviewNumber.totalRevenuePrePeriod = 21302;
        this.overviewNumber.compareSamePeriod = 2;
        this.overviewNumber.taxAgency = 6550;
        this.overviewNumber.customsAgency = 5951;
        this.overviewNumber.otherAgency = 6582;

        this.resetOverviewNumber();
        this.changeFilter();
      },
      'filter.RevenueCateID'(){
        this.resetOverviewNumber();
        this.changeFilter();
      },
      'filter.ProvinceID'(){
        this.filter.DistrictID = null;
        this.filter.DistrictNo = '';
        this.filter.DistrictName = '';
        this.filter.CommuneID = null;
        this.filter.CommuneNo = '';
        this.filter.CommuneName = '';
        this.resetOverviewNumber();
        this.changeFilter();
      },
      'filter.DistrictID'(){
        this.filter.CommuneID = null;
        this.filter.CommuneNo = '';
        this.filter.CommuneName = '';
        this.resetOverviewNumber();
        this.changeFilter();
      },
      'filter.CommuneID'(){
        this.resetOverviewNumber();
        this.changeFilter();
      },
      'filter.CompanyID'(){
        this.changeFilter();
      },
      'filter.FromDate'(){
        this.changeFilter();
      },
      'filter.ToDate'(){
        this.changeFilter();
      },
      'filter.SectorID'(){
        this.changeFilter();
      },
      'filter.PeriodValue'(){
        this.resetOverviewNumber();
        this.changeFilter();
      },
      'filter.PeriodID'(){
        this.resetOverviewNumber();
        this.changeFilter();
      }
    }
  }
</script>
