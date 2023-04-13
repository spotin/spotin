import { Get, Controller, Render, Param, Res } from '@nestjs/common';
import {
  ApiNotFoundResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
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

  @Get()
  @ApiOperation({
    summary: 'Render the spots page',
    description: 'Render the spots page.',
    operationId: 'getSpotsView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/index')
  async getSpotsView() {
    const spots = await this.spotsService.getPublicSpots();
    return { spots };
  }

  @Get(':id')
  @ApiOperation({
    summary: 'Render the specified spot page',
    description: 'Render the specified spot page.',
    operationId: 'getSpotView',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found. Redirect to `/not-found` page.',
  })
  async getSpotView(@Res() res: Response, @Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id);

    if (!spot) {
      // TODO: Is this what we want?
      return res.redirect('not-found');
    }

    const backendUrl = this.configService.get('SPOT_IN_BACKEND_URL');
    const redirection = `${backendUrl}/api/spots/${spot.id}/redirect`;

    try {
      const url = await qrcode.toDataURL(redirection);
      return res.render('spots/[uuid]', {
        title: 'Spot',
        spot: spot,
        qrcode: url,
      });
    } catch (error) {
      console.error(error);
    }
  }

  @Get('create')
  @ApiOperation({
    summary: 'Render the create a new spot page',
    description: 'Render the create a new spot page.',
    operationId: 'createSpotView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/create')
  createSpotView() {
    return;
  }
}
