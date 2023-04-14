import * as qrcode from 'qrcode';
import { Get, Controller, Render, Param, Res } from '@nestjs/common';
import {
  ApiNotFoundResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { User } from '@prisma/client';
import { Response } from 'express';
import { ConfigService } from '@nestjs/config';
import { SpotsService } from '@/spots/spots.service';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';

@ApiTags('Views')
@Controller('spots')
export class SpotsViewsController {
  constructor(
    private readonly spotsService: SpotsService,
    readonly configService: ConfigService,
  ) {}

  @Get()
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the spots page',
    description: 'Render the spots page.',
    operationId: 'getSpotsView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/index')
  async getSpotsView(@AuthUser() user: User) {
    const spots = await this.spotsService.getSpots(user);
    return { spots };
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
  @Render('spots/form')
  createSpotView() {
    return { action: 'POST' };
  }

  @Get(':id/delete')
  @JwtAuth()
  @ApiOperation({
    summary: 'Delete the specified spot and render the list of spots',
    description: 'Delete the specified spot and render the list of spots.',
    operationId: 'deleteSpotView',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots')
  async deleteSpotView(@AuthUser() user: User, @Param('id') id: string) {
    console.log(user);
    await this.spotsService.deleteSpot(id, user);
    return {};
  }

  @Get(':id/edit')
  @ApiOperation({
    summary: 'Render the edit a spot page',
    description: 'Render the edit a spot page.',
    operationId: 'editSpotView',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/form')
  async editSpotView(@Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id);
    return { uuid: id, values: spot, action: 'PATCH' };
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
}
