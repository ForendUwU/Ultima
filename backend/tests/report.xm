<?xml version="1.0" encoding="UTF-8"?>
<testsuites>
  <testsuite name="" tests="80" assertions="382" errors="0" warnings="0" failures="0" skipped="0" time="37.230389">
    <testsuite name="Project Test Suite" tests="80" assertions="382" errors="0" warnings="0" failures="0" skipped="0" time="37.230389">
      <testsuite name="App\Tests\Functional\Controller\AccountFundingControllerTest" file="/app/tests/Functional/Controller/AccountFundingControllerTest.php" tests="3" assertions="13" errors="0" warnings="0" failures="0" skipped="0" time="3.458690">
        <testsuite name="App\Tests\Functional\Controller\AccountFundingControllerTest::testFund" tests="3" assertions="13" errors="0" warnings="0" failures="0" skipped="0" time="3.458690">
          <testcase name="testFund with data set &quot;success&quot;" class="App\Tests\Functional\Controller\AccountFundingControllerTest" classname="App.Tests.Functional.Controller.AccountFundingControllerTest" file="/app/tests/Functional/Controller/AccountFundingControllerTest.php" line="47" assertions="5" time="1.424580"/>
          <testcase name="testFund with data set &quot;missing data&quot;" class="App\Tests\Functional\Controller\AccountFundingControllerTest" classname="App.Tests.Functional.Controller.AccountFundingControllerTest" file="/app/tests/Functional/Controller/AccountFundingControllerTest.php" line="47" assertions="4" time="1.090592"/>
          <testcase name="testFund with data set &quot;unauthorized &quot;" class="App\Tests\Functional\Controller\AccountFundingControllerTest" classname="App.Tests.Functional.Controller.AccountFundingControllerTest" file="/app/tests/Functional/Controller/AccountFundingControllerTest.php" line="47" assertions="4" time="0.943519"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Functional\Controller\AuthorizationControllerTest" file="/app/tests/Functional/Controller/AuthorizationControllerTest.php" tests="7" assertions="23" errors="0" warnings="0" failures="0" skipped="0" time="5.964982">
        <testsuite name="App\Tests\Functional\Controller\AuthorizationControllerTest::testLogin" tests="3" assertions="10" errors="0" warnings="0" failures="0" skipped="0" time="2.552006">
          <testcase name="testLogin with data set &quot;success&quot;" class="App\Tests\Functional\Controller\AuthorizationControllerTest" classname="App.Tests.Functional.Controller.AuthorizationControllerTest" file="/app/tests/Functional/Controller/AuthorizationControllerTest.php" line="54" assertions="3" time="0.943396"/>
          <testcase name="testLogin with data set &quot;wrong password&quot;" class="App\Tests\Functional\Controller\AuthorizationControllerTest" classname="App.Tests.Functional.Controller.AuthorizationControllerTest" file="/app/tests/Functional/Controller/AuthorizationControllerTest.php" line="54" assertions="4" time="0.825793"/>
          <testcase name="testLogin with data set &quot;missing data&quot;" class="App\Tests\Functional\Controller\AuthorizationControllerTest" classname="App.Tests.Functional.Controller.AuthorizationControllerTest" file="/app/tests/Functional/Controller/AuthorizationControllerTest.php" line="54" assertions="3" time="0.782816"/>
        </testsuite>
        <testcase name="testLogout" class="App\Tests\Functional\Controller\AuthorizationControllerTest" classname="App.Tests.Functional.Controller.AuthorizationControllerTest" file="/app/tests/Functional/Controller/AuthorizationControllerTest.php" line="89" assertions="3" time="0.864635"/>
        <testsuite name="App\Tests\Functional\Controller\AuthorizationControllerTest::testRegister1" tests="3" assertions="10" errors="0" warnings="0" failures="0" skipped="0" time="2.548341">
          <testcase name="testRegister1 with data set &quot;success&quot;" class="App\Tests\Functional\Controller\AuthorizationControllerTest" classname="App.Tests.Functional.Controller.AuthorizationControllerTest" file="/app/tests/Functional/Controller/AuthorizationControllerTest.php" line="124" assertions="4" time="0.864414"/>
          <testcase name="testRegister1 with data set &quot;missing data&quot;" class="App\Tests\Functional\Controller\AuthorizationControllerTest" classname="App.Tests.Functional.Controller.AuthorizationControllerTest" file="/app/tests/Functional/Controller/AuthorizationControllerTest.php" line="124" assertions="3" time="0.801405"/>
          <testcase name="testRegister1 with data set &quot;login already in use&quot;" class="App\Tests\Functional\Controller\AuthorizationControllerTest" classname="App.Tests.Functional.Controller.AuthorizationControllerTest" file="/app/tests/Functional/Controller/AuthorizationControllerTest.php" line="124" assertions="3" time="0.882523"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Functional\Controller\PlayingControllerTest" file="/app/tests/Functional/Controller/PlayingControllerTest.php" tests="3" assertions="13" errors="0" warnings="0" failures="0" skipped="0" time="2.755970">
        <testsuite name="App\Tests\Functional\Controller\PlayingControllerTest::testSavePlayingTime" tests="3" assertions="13" errors="0" warnings="0" failures="0" skipped="0" time="2.755970">
          <testcase name="testSavePlayingTime with data set &quot;success&quot;" class="App\Tests\Functional\Controller\PlayingControllerTest" classname="App.Tests.Functional.Controller.PlayingControllerTest" file="/app/tests/Functional/Controller/PlayingControllerTest.php" line="50" assertions="5" time="0.940438"/>
          <testcase name="testSavePlayingTime with data set &quot;missing data&quot;" class="App\Tests\Functional\Controller\PlayingControllerTest" classname="App.Tests.Functional.Controller.PlayingControllerTest" file="/app/tests/Functional/Controller/PlayingControllerTest.php" line="50" assertions="4" time="0.891558"/>
          <testcase name="testSavePlayingTime with data set &quot;unauthorized &quot;" class="App\Tests\Functional\Controller\PlayingControllerTest" classname="App.Tests.Functional.Controller.PlayingControllerTest" file="/app/tests/Functional/Controller/PlayingControllerTest.php" line="50" assertions="4" time="0.923974"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Functional\Controller\PurchaseControllerTest" file="/app/tests/Functional/Controller/PurchaseControllerTest.php" tests="7" assertions="34" errors="0" warnings="0" failures="0" skipped="0" time="6.808742">
        <testsuite name="App\Tests\Functional\Controller\PurchaseControllerTest::testPurchase" tests="3" assertions="14" errors="0" warnings="0" failures="0" skipped="0" time="2.905926">
          <testcase name="testPurchase with data set &quot;success&quot;" class="App\Tests\Functional\Controller\PurchaseControllerTest" classname="App.Tests.Functional.Controller.PurchaseControllerTest" file="/app/tests/Functional/Controller/PurchaseControllerTest.php" line="58" assertions="8" time="1.008973"/>
          <testcase name="testPurchase with data set &quot;missing data&quot;" class="App\Tests\Functional\Controller\PurchaseControllerTest" classname="App.Tests.Functional.Controller.PurchaseControllerTest" file="/app/tests/Functional/Controller/PurchaseControllerTest.php" line="58" assertions="3" time="0.935695"/>
          <testcase name="testPurchase with data set &quot;not enough money&quot;" class="App\Tests\Functional\Controller\PurchaseControllerTest" classname="App.Tests.Functional.Controller.PurchaseControllerTest" file="/app/tests/Functional/Controller/PurchaseControllerTest.php" line="58" assertions="3" time="0.961257"/>
        </testsuite>
        <testcase name="testGetPurchasedGamesSuccess" class="App\Tests\Functional\Controller\PurchaseControllerTest" classname="App.Tests.Functional.Controller.PurchaseControllerTest" file="/app/tests/Functional/Controller/PurchaseControllerTest.php" line="123" assertions="10" time="0.954779"/>
        <testsuite name="App\Tests\Functional\Controller\PurchaseControllerTest::testDeletePurchasedGame" tests="3" assertions="10" errors="0" warnings="0" failures="0" skipped="0" time="2.948038">
          <testcase name="testDeletePurchasedGame with data set &quot;success&quot;" class="App\Tests\Functional\Controller\PurchaseControllerTest" classname="App.Tests.Functional.Controller.PurchaseControllerTest" file="/app/tests/Functional/Controller/PurchaseControllerTest.php" line="180" assertions="4" time="0.989089"/>
          <testcase name="testDeletePurchasedGame with data set &quot;missing data&quot;" class="App\Tests\Functional\Controller\PurchaseControllerTest" classname="App.Tests.Functional.Controller.PurchaseControllerTest" file="/app/tests/Functional/Controller/PurchaseControllerTest.php" line="180" assertions="3" time="1.006994"/>
          <testcase name="testDeletePurchasedGame with data set &quot;unauthorized&quot;" class="App\Tests\Functional\Controller\PurchaseControllerTest" classname="App.Tests.Functional.Controller.PurchaseControllerTest" file="/app/tests/Functional/Controller/PurchaseControllerTest.php" line="180" assertions="3" time="0.951954"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Functional\Controller\ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" tests="12" assertions="61" errors="0" warnings="0" failures="0" skipped="0" time="11.849311">
        <testsuite name="App\Tests\Functional\Controller\ReviewsControllerTest::testCreateGameReview" tests="3" assertions="16" errors="0" warnings="0" failures="0" skipped="0" time="2.908433">
          <testcase name="testCreateGameReview with data set &quot;success&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="82" assertions="10" time="1.042907"/>
          <testcase name="testCreateGameReview with data set &quot;missing data&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="82" assertions="3" time="0.937750"/>
          <testcase name="testCreateGameReview with data set &quot;user already has review&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="82" assertions="3" time="0.927777"/>
        </testsuite>
        <testsuite name="App\Tests\Functional\Controller\ReviewsControllerTest::testGetGameReviews" tests="2" assertions="14" errors="0" warnings="0" failures="0" skipped="0" time="1.893479">
          <testcase name="testGetGameReviews with data set &quot;with review creating&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="154" assertions="12" time="1.007645"/>
          <testcase name="testGetGameReviews with data set &quot;without review creating&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="154" assertions="2" time="0.885834"/>
        </testsuite>
        <testsuite name="App\Tests\Functional\Controller\ReviewsControllerTest::testChangeGameReviewContent" tests="3" assertions="16" errors="0" warnings="0" failures="0" skipped="0" time="2.872168">
          <testcase name="testChangeGameReviewContent with data set &quot;success&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="214" assertions="10" time="0.995819"/>
          <testcase name="testChangeGameReviewContent with data set &quot;missing data&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="214" assertions="3" time="0.941149"/>
          <testcase name="testChangeGameReviewContent with data set &quot;user doesn't have a review&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="214" assertions="3" time="0.935200"/>
        </testsuite>
        <testsuite name="App\Tests\Functional\Controller\ReviewsControllerTest::testDeleteReview" tests="2" assertions="7" errors="0" warnings="0" failures="0" skipped="0" time="1.939087">
          <testcase name="testDeleteReview with data set &quot;success&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="286" assertions="4" time="0.986338"/>
          <testcase name="testDeleteReview with data set &quot;user doesn't have a review&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="286" assertions="3" time="0.952749"/>
        </testsuite>
        <testsuite name="App\Tests\Functional\Controller\ReviewsControllerTest::testGetUserReviewContentByGameId" tests="2" assertions="8" errors="0" warnings="0" failures="0" skipped="0" time="2.236144">
          <testcase name="testGetUserReviewContentByGameId with data set &quot;success&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="345" assertions="4" time="1.033161"/>
          <testcase name="testGetUserReviewContentByGameId with data set &quot;user doesn't have a review&quot;" class="App\Tests\Functional\Controller\ReviewsControllerTest" classname="App.Tests.Functional.Controller.ReviewsControllerTest" file="/app/tests/Functional/Controller/ReviewsControllerTest.php" line="345" assertions="4" time="1.202982"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Functional\Controller\UserInfoControllerTest" file="/app/tests/Functional/Controller/UserInfoControllerTest.php" tests="3" assertions="32" errors="0" warnings="0" failures="0" skipped="0" time="3.946695">
        <testcase name="testGetUserInfoSuccess" class="App\Tests\Functional\Controller\UserInfoControllerTest" classname="App.Tests.Functional.Controller.UserInfoControllerTest" file="/app/tests/Functional/Controller/UserInfoControllerTest.php" line="45" assertions="15" time="1.210631"/>
        <testsuite name="App\Tests\Functional\Controller\UserInfoControllerTest::testGetUsersMostPlayedGames1" tests="2" assertions="17" errors="0" warnings="0" failures="0" skipped="0" time="2.736064">
          <testcase name="testGetUsersMostPlayedGames1 with data set &quot;success&quot;" class="App\Tests\Functional\Controller\UserInfoControllerTest" classname="App.Tests.Functional.Controller.UserInfoControllerTest" file="/app/tests/Functional/Controller/UserInfoControllerTest.php" line="92" assertions="13" time="1.316893"/>
          <testcase name="testGetUsersMostPlayedGames1 with data set &quot;unauthorized&quot;" class="App\Tests\Functional\Controller\UserInfoControllerTest" classname="App.Tests.Functional.Controller.UserInfoControllerTest" file="/app/tests/Functional/Controller/UserInfoControllerTest.php" line="92" assertions="4" time="1.419172"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Functional\Repository\UserRepositoryTest" file="/app/tests/Functional/Repository/UserRepositoryTest.php" tests="2" assertions="2" errors="0" warnings="0" failures="0" skipped="0" time="2.113022">
        <testcase name="testUpgradePasswordSuccess" class="App\Tests\Functional\Repository\UserRepositoryTest" classname="App.Tests.Functional.Repository.UserRepositoryTest" file="/app/tests/Functional/Repository/UserRepositoryTest.php" line="31" assertions="1" time="1.002334"/>
        <testcase name="testUpgradePasswordNotSupported" class="App\Tests\Functional\Repository\UserRepositoryTest" classname="App.Tests.Functional.Repository.UserRepositoryTest" file="/app/tests/Functional/Repository/UserRepositoryTest.php" line="51" assertions="1" time="1.110688"/>
      </testsuite>
      <testsuite name="App\Tests\Unit\Entity\GameTest" file="/app/tests/Unit/Entity/GameTest.php" tests="2" assertions="10" errors="0" warnings="0" failures="0" skipped="0" time="0.002925">
        <testcase name="testCreateEmptyGameEntity" class="App\Tests\Unit\Entity\GameTest" classname="App.Tests.Unit.Entity.GameTest" file="/app/tests/Unit/Entity/GameTest.php" line="11" assertions="4" time="0.001387"/>
        <testcase name="testCreateNotEmptyGameEntity" class="App\Tests\Unit\Entity\GameTest" classname="App.Tests.Unit.Entity.GameTest" file="/app/tests/Unit/Entity/GameTest.php" line="21" assertions="6" time="0.001538"/>
      </testsuite>
      <testsuite name="App\Tests\Unit\Entity\PurchasedGameTest" file="/app/tests/Unit/Entity/PurchasedGameTest.php" tests="2" assertions="7" errors="0" warnings="0" failures="0" skipped="0" time="0.012823">
        <testcase name="testCreateEmptyPurchasedGame" class="App\Tests\Unit\Entity\PurchasedGameTest" classname="App.Tests.Unit.Entity.PurchasedGameTest" file="/app/tests/Unit/Entity/PurchasedGameTest.php" line="12" assertions="4" time="0.007302"/>
        <testcase name="testCreateNotEmptyPurchasedGame" class="App\Tests\Unit\Entity\PurchasedGameTest" classname="App.Tests.Unit.Entity.PurchasedGameTest" file="/app/tests/Unit/Entity/PurchasedGameTest.php" line="22" assertions="3" time="0.005520"/>
      </testsuite>
      <testsuite name="App\Tests\Unit\Entity\UserTest" file="/app/tests/Unit/Entity/UserTest.php" tests="8" assertions="30" errors="0" warnings="0" failures="0" skipped="0" time="0.013937">
        <testcase name="testCreateEmptyUserEntity" class="App\Tests\Unit\Entity\UserTest" classname="App.Tests.Unit.Entity.UserTest" file="/app/tests/Unit/Entity/UserTest.php" line="30" assertions="6" time="0.002081"/>
        <testcase name="testCreateNotEmptyUserEntity" class="App\Tests\Unit\Entity\UserTest" classname="App.Tests.Unit.Entity.UserTest" file="/app/tests/Unit/Entity/UserTest.php" line="41" assertions="12" time="0.004237"/>
        <testsuite name="App\Tests\Unit\Entity\UserTest::testValidateLogin" tests="3" assertions="6" errors="0" warnings="0" failures="0" skipped="0" time="0.004716">
          <testcase name="testValidateLogin with data set &quot;smallLogin&quot;" class="App\Tests\Unit\Entity\UserTest" classname="App.Tests.Unit.Entity.UserTest" file="/app/tests/Unit/Entity/UserTest.php" line="77" assertions="2" time="0.002883"/>
          <testcase name="testValidateLogin with data set &quot;bigLogin&quot;" class="App\Tests\Unit\Entity\UserTest" classname="App.Tests.Unit.Entity.UserTest" file="/app/tests/Unit/Entity/UserTest.php" line="77" assertions="2" time="0.000917"/>
          <testcase name="testValidateLogin with data set &quot;loginWithInvalidCharacters&quot;" class="App\Tests\Unit\Entity\UserTest" classname="App.Tests.Unit.Entity.UserTest" file="/app/tests/Unit/Entity/UserTest.php" line="77" assertions="2" time="0.000915"/>
        </testsuite>
        <testsuite name="App\Tests\Unit\Entity\UserTest::testValidateNickname" tests="3" assertions="6" errors="0" warnings="0" failures="0" skipped="0" time="0.002903">
          <testcase name="testValidateNickname with data set &quot;smallNickname&quot;" class="App\Tests\Unit\Entity\UserTest" classname="App.Tests.Unit.Entity.UserTest" file="/app/tests/Unit/Entity/UserTest.php" line="99" assertions="2" time="0.001016"/>
          <testcase name="testValidateNickname with data set &quot;bigNickname&quot;" class="App\Tests\Unit\Entity\UserTest" classname="App.Tests.Unit.Entity.UserTest" file="/app/tests/Unit/Entity/UserTest.php" line="99" assertions="2" time="0.000952"/>
          <testcase name="testValidateNickname with data set &quot;NicknameWithInvalidCharacters&quot;" class="App\Tests\Unit\Entity\UserTest" classname="App.Tests.Unit.Entity.UserTest" file="/app/tests/Unit/Entity/UserTest.php" line="99" assertions="2" time="0.000935"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Unit\Security\ApiTokenFailureHandlerTest" file="/app/tests/Unit/Security/ApiTokenFailureHandlerTest.php" tests="1" assertions="1" errors="0" warnings="0" failures="0" skipped="0" time="0.007448">
        <testcase name="testOnAuthenticationFailure" class="App\Tests\Unit\Security\ApiTokenFailureHandlerTest" classname="App.Tests.Unit.Security.ApiTokenFailureHandlerTest" file="/app/tests/Unit/Security/ApiTokenFailureHandlerTest.php" line="14" assertions="1" time="0.007448"/>
      </testsuite>
      <testsuite name="App\Tests\Unit\Security\ApiTokenHandlerTest" file="/app/tests/Unit/Security/ApiTokenHandlerTest.php" tests="3" assertions="11" errors="0" warnings="0" failures="0" skipped="0" time="0.087707">
        <testsuite name="App\Tests\Unit\Security\ApiTokenHandlerTest::testGetUserBadgeFrom" tests="3" assertions="11" errors="0" warnings="0" failures="0" skipped="0" time="0.087707">
          <testcase name="testGetUserBadgeFrom with data set &quot;success&quot;" class="App\Tests\Unit\Security\ApiTokenHandlerTest" classname="App.Tests.Unit.Security.ApiTokenHandlerTest" file="/app/tests/Unit/Security/ApiTokenHandlerTest.php" line="41" assertions="4" time="0.077276"/>
          <testcase name="testGetUserBadgeFrom with data set &quot;bad credentials&quot;" class="App\Tests\Unit\Security\ApiTokenHandlerTest" classname="App.Tests.Unit.Security.ApiTokenHandlerTest" file="/app/tests/Unit/Security/ApiTokenHandlerTest.php" line="41" assertions="3" time="0.004602"/>
          <testcase name="testGetUserBadgeFrom with data set &quot;expired token&quot;" class="App\Tests\Unit\Security\ApiTokenHandlerTest" classname="App.Tests.Unit.Security.ApiTokenHandlerTest" file="/app/tests/Unit/Security/ApiTokenHandlerTest.php" line="41" assertions="4" time="0.005829"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Unit\Service\AccountFundingServiceTest" file="/app/tests/Unit/Service/AccountFundingServiceTest.php" tests="1" assertions="3" errors="0" warnings="0" failures="0" skipped="0" time="0.004673">
        <testcase name="testFund" class="App\Tests\Unit\Service\AccountFundingServiceTest" classname="App.Tests.Unit.Service.AccountFundingServiceTest" file="/app/tests/Unit/Service/AccountFundingServiceTest.php" line="23" assertions="3" time="0.004673"/>
      </testsuite>
      <testsuite name="App\Tests\Unit\Service\AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" tests="11" assertions="31" errors="0" warnings="0" failures="0" skipped="0" time="0.071531">
        <testsuite name="App\Tests\Unit\Service\AuthorizationServiceTest::testLogin" tests="2" assertions="9" errors="0" warnings="0" failures="0" skipped="0" time="0.019337">
          <testcase name="testLogin with data set &quot;success&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="68" assertions="5" time="0.012765"/>
          <testcase name="testLogin with data set &quot;wrong password&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="68" assertions="4" time="0.006572"/>
        </testsuite>
        <testsuite name="App\Tests\Unit\Service\AuthorizationServiceTest::testLogout" tests="2" assertions="8" errors="0" warnings="0" failures="0" skipped="0" time="0.018787">
          <testcase name="testLogout with data set &quot;success&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="106" assertions="4" time="0.013502"/>
          <testcase name="testLogout with data set &quot;user already unauthorized&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="106" assertions="4" time="0.005285"/>
        </testsuite>
        <testcase name="testRegisterSuccess" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="130" assertions="3" time="0.015900"/>
        <testsuite name="App\Tests\Unit\Service\AuthorizationServiceTest::testValidatePassword" tests="6" assertions="11" errors="0" warnings="0" failures="0" skipped="0" time="0.017508">
          <testcase name="testValidatePassword with data set &quot;validPassword&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="146" assertions="1" time="0.002985"/>
          <testcase name="testValidatePassword with data set &quot;tooSmallPassword&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="146" assertions="2" time="0.002061"/>
          <testcase name="testValidatePassword with data set &quot;tooBigPassword&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="146" assertions="2" time="0.001931"/>
          <testcase name="testValidatePassword with data set &quot;passwordWithInvalidSymbols&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="146" assertions="2" time="0.003366"/>
          <testcase name="testValidatePassword with data set &quot;passwordWithoutDigits&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="146" assertions="2" time="0.001981"/>
          <testcase name="testValidatePassword with data set &quot;passwordWithoutSymbols&quot;" class="App\Tests\Unit\Service\AuthorizationServiceTest" classname="App.Tests.Unit.Service.AuthorizationServiceTest" file="/app/tests/Unit/Service/AuthorizationServiceTest.php" line="146" assertions="2" time="0.005183"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Unit\Service\PlayingServiceTest" file="/app/tests/Unit/Service/PlayingServiceTest.php" tests="1" assertions="5" errors="0" warnings="0" failures="0" skipped="0" time="0.025274">
        <testcase name="testSavePlayingTime" class="App\Tests\Unit\Service\PlayingServiceTest" classname="App.Tests.Unit.Service.PlayingServiceTest" file="/app/tests/Unit/Service/PlayingServiceTest.php" line="29" assertions="5" time="0.025274"/>
      </testsuite>
      <testsuite name="App\Tests\Unit\Service\PurchaseServiceTest" file="/app/tests/Unit/Service/PurchaseServiceTest.php" tests="4" assertions="30" errors="0" warnings="0" failures="0" skipped="0" time="0.024503">
        <testsuite name="App\Tests\Unit\Service\PurchaseServiceTest::testPurchase" tests="3" assertions="18" errors="0" warnings="0" failures="0" skipped="0" time="0.018819">
          <testcase name="testPurchase with data set &quot;success&quot;" class="App\Tests\Unit\Service\PurchaseServiceTest" classname="App.Tests.Unit.Service.PurchaseServiceTest" file="/app/tests/Unit/Service/PurchaseServiceTest.php" line="50" assertions="6" time="0.006340"/>
          <testcase name="testPurchase with data set &quot;not enough money&quot;" class="App\Tests\Unit\Service\PurchaseServiceTest" classname="App.Tests.Unit.Service.PurchaseServiceTest" file="/app/tests/Unit/Service/PurchaseServiceTest.php" line="50" assertions="6" time="0.006427"/>
          <testcase name="testPurchase with data set &quot;game already purchased&quot;" class="App\Tests\Unit\Service\PurchaseServiceTest" classname="App.Tests.Unit.Service.PurchaseServiceTest" file="/app/tests/Unit/Service/PurchaseServiceTest.php" line="50" assertions="6" time="0.006052"/>
        </testsuite>
        <testcase name="testGetPurchasedGames" class="App\Tests\Unit\Service\PurchaseServiceTest" classname="App.Tests.Unit.Service.PurchaseServiceTest" file="/app/tests/Unit/Service/PurchaseServiceTest.php" line="86" assertions="12" time="0.005684"/>
      </testsuite>
      <testsuite name="App\Tests\Unit\Service\ReviewsServiceTest" file="/app/tests/Unit/Service/ReviewsServiceTest.php" tests="7" assertions="37" errors="0" warnings="0" failures="0" skipped="0" time="0.064088">
        <testsuite name="App\Tests\Unit\Service\ReviewsServiceTest::testCreateGameReview" tests="2" assertions="9" errors="0" warnings="0" failures="0" skipped="0" time="0.022616">
          <testcase name="testCreateGameReview with data set &quot;success&quot;" class="App\Tests\Unit\Service\ReviewsServiceTest" classname="App.Tests.Unit.Service.ReviewsServiceTest" file="/app/tests/Unit/Service/ReviewsServiceTest.php" line="49" assertions="4" time="0.016022"/>
          <testcase name="testCreateGameReview with data set &quot;user already has review on this game&quot;" class="App\Tests\Unit\Service\ReviewsServiceTest" classname="App.Tests.Unit.Service.ReviewsServiceTest" file="/app/tests/Unit/Service/ReviewsServiceTest.php" line="49" assertions="5" time="0.006595"/>
        </testsuite>
        <testcase name="testGetGameReviews" class="App\Tests\Unit\Service\ReviewsServiceTest" classname="App.Tests.Unit.Service.ReviewsServiceTest" file="/app/tests/Unit/Service/ReviewsServiceTest.php" line="102" assertions="11" time="0.011849"/>
        <testcase name="testChangeGameReviewContent" class="App\Tests\Unit\Service\ReviewsServiceTest" classname="App.Tests.Unit.Service.ReviewsServiceTest" file="/app/tests/Unit/Service/ReviewsServiceTest.php" line="144" assertions="5" time="0.008943"/>
        <testcase name="testDeleteUsersReviewContent" class="App\Tests\Unit\Service\ReviewsServiceTest" classname="App.Tests.Unit.Service.ReviewsServiceTest" file="/app/tests/Unit/Service/ReviewsServiceTest.php" line="173" assertions="4" time="0.007577"/>
        <testsuite name="App\Tests\Unit\Service\ReviewsServiceTest::testGetUserReviewContentByUserLoginAndGameId1" tests="2" assertions="8" errors="0" warnings="0" failures="0" skipped="0" time="0.013102">
          <testcase name="testGetUserReviewContentByUserLoginAndGameId1 with data set &quot;success&quot;" class="App\Tests\Unit\Service\ReviewsServiceTest" classname="App.Tests.Unit.Service.ReviewsServiceTest" file="/app/tests/Unit/Service/ReviewsServiceTest.php" line="204" assertions="4" time="0.006631"/>
          <testcase name="testGetUserReviewContentByUserLoginAndGameId1 with data set &quot;user doesn't have a review&quot;" class="App\Tests\Unit\Service\ReviewsServiceTest" classname="App.Tests.Unit.Service.ReviewsServiceTest" file="/app/tests/Unit/Service/ReviewsServiceTest.php" line="204" assertions="4" time="0.006471"/>
        </testsuite>
      </testsuite>
      <testsuite name="App\Tests\Unit\Service\UserInfoServiceTest" file="/app/tests/Unit/Service/UserInfoServiceTest.php" tests="3" assertions="39" errors="0" warnings="0" failures="0" skipped="0" time="0.018069">
        <testcase name="testGetUserInfo" class="App\Tests\Unit\Service\UserInfoServiceTest" classname="App.Tests.Unit.Service.UserInfoServiceTest" file="/app/tests/Unit/Service/UserInfoServiceTest.php" line="33" assertions="17" time="0.005877"/>
        <testcase name="testGetUsersMostPlayedGames" class="App\Tests\Unit\Service\UserInfoServiceTest" classname="App.Tests.Unit.Service.UserInfoServiceTest" file="/app/tests/Unit/Service/UserInfoServiceTest.php" line="61" assertions="15" time="0.006597"/>
        <testcase name="testUpdateUserInfo" class="App\Tests\Unit\Service\UserInfoServiceTest" classname="App.Tests.Unit.Service.UserInfoServiceTest" file="/app/tests/Unit/Service/UserInfoServiceTest.php" line="103" assertions="7" time="0.005595"/>
      </testsuite>
    </testsuite>
  </testsuite>
</testsuites>
