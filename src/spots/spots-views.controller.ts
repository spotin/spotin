import {
  Get,
  Controller,
  Render,
  UseGuards,
  Param,
  UseFilters,
  Res,
} from '@nestjs/common';
import {
  ApiNotFoundResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { JwtAuthGuard } from 'src/auth/jwt/jwt-auth-guards';
import { UnauthorizedExceptionFilter } from 'src/filters/unauthorized-exception.filter';
import { SpotsService } from './spots.service';
import * as qrcode from 'qrcode';
import { Response } from 'express';
import { ConfigService } from '@nestjs/config';

@ApiTags('Views')
@Controller('spots')
export class SpotsViewsController {
  constructor(
    private readonly spotsService: SpotsService,
    readonly configService: ConfigService,
  ) {}

  @UseGuards(JwtAuthGuard)
  @Get('list')
  @UseFilters(new UnauthorizedExceptionFilter())
  @Render('spots/list')
  @ApiOperation({
    summary: 'Render the spots list page',
    description: 'Render the spots list page.',
    operationId: 'list',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  async root() {
    const spots = await this.spotsService.getSpots();
    return { spots };
  }

  @Get('latest')
  @Render('spots/latest')
  @ApiOperation({
    summary: 'Render the latest spots page',
    description: 'Render the latest spots page.',
    operationId: 'latest',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  async getLatestSpots() {
    const spots = await this.spotsService.getPublicSpots();
    return { spots };
  }

  @Get('govtech')
  @Render('one-page/index')
  @ApiOperation({
    summary: 'Render the one page page',
    description: 'Render the one page page.',
    operationId: 'onePage',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  async getOnePage() {
    const spots = await this.spotsService.getSpots();
    return { spots };
  }

  @Get(':uuid/delete')
  @Render('spots/list')
  @ApiOperation({
    summary: 'Render the spots list page while deleting a spot',
    description: 'Render the spots list page while deleting a spot.',
    operationId: 'deleteAndList',
  })
  @ApiParam({
    name: 'uuid',
    description: 'Spot UUID',
    type: String,
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @ApiNotFoundResponse({
    description: 'Spot not found.',
  })
  async deleteSpot(@Param('uuid') uuid: string) {
    await this.spotsService.deleteSpot(uuid);
    const spots = await this.spotsService.getSpots();
    return { spots };
  }

  @Get(':uuid/edit')
  @Render('spots/form')
  @ApiOperation({
    summary: 'Render the spots form page while editing a spot',
    description: 'Render the spots form page while editing a spot.',
    operationId: 'editAndForm',
  })
  @ApiParam({
    name: 'uuid',
    description: 'Spot UUID',
    type: String,
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @ApiNotFoundResponse({
    description: 'Spot not found.',
  })
  async editSpot(@Param('uuid') uuid: string) {
    const spot = await this.spotsService.getSpot(uuid);
    return { values: spot, action: 'PATCH', uuid };
  }

  @Get('create')
  @Render('spots/form')
  @ApiOperation({
    summary: 'Render the spots form page',
    description: 'Render the spots form page.',
    operationId: 'form',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  createSpot() {
    return { action: 'POST' };
  }

  @UseGuards(JwtAuthGuard)
  @Get(':uuid')
  @UseFilters(new UnauthorizedExceptionFilter())
  @ApiOperation({
    summary: 'Render the spot page',
    description: 'Render the spot page.',
    operationId: 'spot',
  })
  @ApiParam({
    name: 'uuid',
    description: 'Spot UUID',
    type: String,
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @ApiNotFoundResponse({
    description: 'Spot not found.',
  })
  async getSpot(@Res() res: Response, @Param('uuid') uuid: string) {
    const spot = await this.spotsService.getSpot(uuid);

    if (spot) {
      const backendUrl = this.configService.get('SPOT_IN_BACKEND_URL');
      const redirection = `${backendUrl}/api/spots/${spot.id}/redirect`;

      qrcode
        .toDataURL(redirection)
        .then((url) => {
          res.render('spots/[uuid]', {
            title: 'Spot',
            spot: spot,
            qrcode: url,
          });
        })
        .catch((err) => {
          console.error(err);
        });
    }
  }
}
