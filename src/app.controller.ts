import { Get, Controller, Render } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';

@ApiTags('Views')
@Controller()
export class AppController {
  @Get()
  @ApiOperation({
    summary: 'Render the main page',
    description: 'Render the main page.',
    operationId: 'root',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('index')
  root() {
    return;
  }

  @Get('api')
  @ApiOperation({
    summary: 'Render the Swagger UI page',
    description: 'Render the Swagger UI page.',
    operationId: 'api',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  api() {
    return;
  }
}
