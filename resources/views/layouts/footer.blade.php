<footer class="footer">
  <div class=" container-fluid ">
    <nav>
      <ul>
        <li>
          <a href="/" target="_blank">
            {{__(" About Us")}}
          </a>
        </li>
        <li>
          <a href="/" target="_blank">
            {{__(" Blog")}}
          </a>
        </li>
      </ul>
    </nav>
    <div class="copyright" id="copyright">
      Copyrights &copy;
      <script>
        document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))
      </script>
      {{--, {{__(" Designed by")}}
      <a href="/" target="_blank">{{__(" Mohsin Javeed")}}</a>&
      <a href="/" target="_blank">{{__(" Smiran Kaur")}}</a>{{__(" . Coded by")}}
      <a href="/" target="_blank">{{__(" Mohsin Javeed ")}}</a>--}}
    </div>
  </div>
</footer>